<?php

namespace App\Application\Telegram\Conversation\ConversationHandlers;

use App\Application\Telegram\Conversation\Abstracts\BaseConversationAbstract;
use App\Application\Telegram\Conversation\Enums\ConversationTopic;
use App\Domain\Patient\Actions\SaveCheckupMeasurementsAction;
use App\Models\Checkup;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;

class MeasurementsConversation extends BaseConversationAbstract
{
    protected Checkup $checkup;
    public function setCheckup(Checkup $checkup)
    {
        $this->checkup = $checkup;
    }
    public function init(): ?string
    {
        $this->reply(
            [
                'text' => 'Начинаю собирать замер',
            ]
        );

        return 'confirm';
    }

    public function confirm()
    {
        $message = $this->telegram->getWebhookUpdate()->getMessage();

        Log::info('Confirm', ['message' => $message]);

        app(SaveCheckupMeasurementsAction::class)->execute($this->checkup, $message);

        return null;
    }

    public function getTopic(): ConversationTopic
    {
        return ConversationTopic::StartMeasurements;
    }
}
