<?php

namespace App\Application\Telegram\Conversation\Actions;

use App\Application\Telegram\Conversation\ConversationHandlers\MeasurementsConversation;
use App\Application\Telegram\Conversation\ConversationHandlers\QuestionConversation;
use App\Application\Telegram\Conversation\Enums\ConversationTopic;
use App\Models\Checkup;
use App\Models\Conversation;
use App\Models\Patient;

class CheckForConversations
{
    public function __construct(protected ProceedConversationAction $proceedConversationAction)
    {
    }

    public function execute(Patient $patient)
    {
        $conversation = Conversation::where('patient_id', $patient->id)
            ->where('finished', false)
            ->orderBy('created_at', 'desc')->first();

        if (!$conversation) {
            return;
        }

        switch ($conversation->topic) {
            case ConversationTopic::SendQuestion:
                $this->proceedConversationAction->execute($patient, app(QuestionConversation::class));
                break;
            case ConversationTopic::StartMeasurements:
                $measurementsConversation = app(MeasurementsConversation::class);
                $measurementsConversation->setCheckup(Checkup::find($conversation->data['checkup_id']));
                $this->proceedConversationAction->execute($patient, $measurementsConversation);
        }
    }
}
