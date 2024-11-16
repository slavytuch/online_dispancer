<?php

namespace App\Application\Telegram;

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
        $user = null;
        if ($from && !$user = PatientHelper::getByTelegramId($from->id)) {
            $user = Patient::factory(1, [
                'telegram_id' => $from->id,
                'name' => $from->first_name ?? $from->username,
            ])->create();
        }

        if (!$user) {
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


                break;
        }

        $update = $this->telegram->commandsHandler(true);
        $this->logger->info('webhook', ['update' => $update]);
    }
}
