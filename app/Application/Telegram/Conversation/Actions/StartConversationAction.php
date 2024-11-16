<?php

namespace App\Application\Telegram\Conversation\Actions;

use App\Application\Telegram\Conversation\Abstracts\ConversationHandlerInterface;
use App\Models\Conversation;
use App\Models\Patient;

class StartConversationAction
{
    public function execute(Patient $patient, ConversationHandlerInterface $conversationHandler, ?array $data = null)
    {
        if (Conversation::where('patient_id', $patient->id)
            ->where('finished', false)
            ->exists()
        ) {
            return;
        }

        $nextStage = $conversationHandler->init();

        Conversation::create([
            'patient_id' => $patient->id,
            'topic' => $conversationHandler->getTopic(),
            'next_stage' => $nextStage,
            'data' => $data
        ]);
    }
}
