<?php

namespace App\Application\Telegram;

use App\Application\Telegram\Conversation\Actions\CheckForConversations;
use App\Application\Telegram\Conversation\Actions\ProceedConversationAction;
use App\Models\Patient;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;
use Telegram\Bot\Api;
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
            $patient = Patient::factory(1, [
                'telegram_id' => $from->id,
                'name' => $from->first_name ?? $from->username,
            ])->create();
        }

        \Log::info('message', ['message' => $relatedObject]);

        if (!$patient) {
            throw new \Exception('Нет пользователя для обработки');
        }

        switch (get_class($relatedObject)) {
            case CallbackQuery::class:
                $factory = new InlineActionFactory($this->telegram, $relatedObject);
                $action = $factory->getAction();
                if ($action) {
                    $action->execute();
                }
                return;
            case Message::class:
                app(CheckForConversations::class)->execute($patient);

                break;
        }

        $update = $this->telegram->commandsHandler(true);
        $this->logger->info('webhook', ['update' => $update]);
    }
}
