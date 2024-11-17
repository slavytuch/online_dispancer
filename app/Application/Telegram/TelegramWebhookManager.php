<?php

namespace App\Application\Telegram;

use App\Application\Telegram\Actions\LogAction;
use App\Application\Telegram\Conversation\Actions\CheckForConversations;
use App\Domain\Patient\Actions\RegisterPatientAction;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Message;

class TelegramWebhookManager
{
    public function __construct(
        protected readonly Api $telegram,
        protected LoggerInterface $logger,
    ) {
    }

    /**
     * Обработка запроса от ТГ
     *
     * @param Request $request
     */
    public function handleRequest(Request $request): void
    {
        $this->logger->info('request', ['request' => $request->toArray()]);
        $update = $this->telegram->getWebhookUpdate();
        $relatedObject = $update->getRelatedObject();

        $from = $relatedObject->from;
        \Log::info('from', ['from' => $from]);
        $patient = null;
        if ($from && !$patient = PatientHelper::getByTelegramId($from->id)) {
            $patient = app(RegisterPatientAction::class)->execute($from->id, $from->first_name, $from->last_name);
        }

        \Log::info('message', ['message' => $relatedObject]);

        if (!$patient) {
            throw new \Exception('Нет пользователя для обработки');
        }

        $logAction = app(LogAction::class);

        switch (get_class($relatedObject)) {
            case CallbackQuery::class:
                $logAction->execute($from->id, $relatedObject->from->id, ['callback_query' => $relatedObject]);

                $factory = new InlineActionFactory($this->telegram, $relatedObject);
                $action = $factory->getAction();
                if ($action) {
                    $action->execute();
                }
                return;
            case Message::class:
                if ($relatedObject->contact && !$patient->phone) {
                    $patient->phone = $relatedObject->contact->phone_number;
                    $patient->save();
                    $this->telegram->sendMessage([
                        'chat_id' => $from->id,
                        'text' => 'Спасибо, телефон успешно сохранён',
                        'reply_markup' => Keyboard::remove()
                    ]);
                    break;
                }

                $logAction->execute($from->id, $relatedObject->messageId, ['message' => $relatedObject]);
                app(CheckForConversations::class)->execute($patient);

                break;
            default:
                $logAction->execute($from->id, 0, ['message' => $relatedObject]);
        }

        $update = $this->telegram->commandsHandler(true);
        $this->logger->info('webhook', ['update' => $update]);
    }
}
