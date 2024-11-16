<?php

namespace App\Application\Telegram\Conversation\Actions;

use App\Application\Telegram\Conversation\Abstracts\ConversationHandlerInterface;
use App\Models\Conversation;
use App\Models\Patient;

class StartConversationAction
{
    public function execute(Patient $patient, ConversationHandlerInterface $conversationHandler, ?array $data = null)
    {
        $nextStage = $conversationHandler->init();

        Conversation::create([
            'patient_id' => $patient->id,
            'topic' => $conversationHandler->getTopic(),
            'next_stage' => $nextStage,
            'data' => $data
        ]);
    }
}
