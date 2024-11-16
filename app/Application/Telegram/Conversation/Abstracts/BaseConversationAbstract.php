<?php

namespace App\Application\Telegram\Conversation\Abstracts;

use App\Models\Conversation;
use App\Models\Patient;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

abstract class BaseConversationAbstract implements ConversationHandlerInterface
{
    protected readonly Patient $patient;

    public function __construct(
        protected Api $telegram,
        protected readonly Conversation $conversation,
    ) {
    }

    /**
     * @param array $params
     * @throws TelegramSDKException
     */
    protected function reply(array $params)
    {
        $this->telegram->sendMessage(
            array_merge(['chat_id' => $this->telegram->getWebhookUpdate()->getChat()->get('id')], $params)
        );
    }
}
