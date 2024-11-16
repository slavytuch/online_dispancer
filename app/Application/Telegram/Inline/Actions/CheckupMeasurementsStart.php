<?php

namespace App\Application\Telegram\Inline\Actions;

use App\Application\Telegram\Conversation\Actions\StartConversationAction;
use App\Application\Telegram\Conversation\ConversationHandlers\MeasurementsConversation;
use App\Application\Telegram\Inline\Abstract\BaseInlineActionAbstract;
use App\Models\Checkup;

class CheckupMeasurementsStart extends BaseInlineActionAbstract
{
    public function execute()
    {
        $parts = explode(':', $this->relatedObject->data);
        $checkupId = end($parts);

        $checkup = Checkup::find($checkupId);

        $this->answer('Жду показаний по ' . $checkup->description);

        $conversation = app(MeasurementsConversation::class);
        $conversation->setCheckup($checkup);

        app(StartConversationAction::class)->execute($checkup->patient, $conversation);
    }
}
