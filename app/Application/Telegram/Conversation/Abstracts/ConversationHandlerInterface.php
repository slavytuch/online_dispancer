<?php

namespace App\Application\Telegram\Conversation\Abstracts;

use App\Application\Telegram\Conversation\Enums\ConversationTopic;

interface ConversationHandlerInterface
{
    public function init(): ?string;

    public function getTopic(): ConversationTopic;
}
