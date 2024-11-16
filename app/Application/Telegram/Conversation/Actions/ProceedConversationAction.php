<?php

namespace App\Application\Telegram\Conversation\Actions;

use App\Application\Telegram\Conversation\Abstracts\ConversationHandlerInterface;
use App\Models\Conversation;
use App\Models\Patient;

class ProceedConversationAction
{
    public function execute(Patient $patient, ConversationHandlerInterface $conversationHandler)
    {
        $conversation = Conversation::where('patient_id', $patient->id)->where(
            'topic',
            $conversationHandler->getTopic()
        )->orderBy('created_at', 'desc')->first();

        if (!$conversation->next_stage) {
            $conversation->finished = true;
            $conversation->save();
            return;
        }

        $nextStage = $conversationHandler->$conversation->next_stage;
        Conversation::create([
            'patient_id' => $patient->id,
            'topic' => $conversationHandler->getTopic(),
            'next_stage' => $nextStage
        ]);
    }
}
