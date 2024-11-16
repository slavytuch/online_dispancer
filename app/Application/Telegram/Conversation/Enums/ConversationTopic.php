<?php

namespace App\Application\Telegram\Conversation\Enums;

enum ConversationTopic: string
{
    case StartMeasurements = 'start-measurements';
    case SendQuestion = 'send-question';
}
