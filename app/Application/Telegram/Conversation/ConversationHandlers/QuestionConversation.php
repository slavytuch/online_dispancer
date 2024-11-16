<?php

namespace App\Application\Telegram\Conversation\ConversationHandlers;

use App\Application\Telegram\Conversation\Abstracts\BaseConversationAbstract;
use App\Application\Telegram\Conversation\Enums\ConversationTopic;
use Illuminate\Support\Facades\Log;

class QuestionConversation extends BaseConversationAbstract
{

    public function init(): ?string
    {
        $this->reply([
            'text' => 'Какой вопрос вы хотите задать врачу?'
        ]);

        return 'confirm';
    }

    public function confirm()
    {
        $message = $this->telegram->getWebhookUpdate()->getMessage();

        Log::info('Confirm', ['message' => $message]);

        return null;
    }

    public function getTopic(): ConversationTopic
    {
        return ConversationTopic::SendQuestion;
    }
}
