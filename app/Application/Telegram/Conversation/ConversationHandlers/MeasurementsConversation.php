<?php

namespace App\Application\Telegram\Conversation\ConversationHandlers;

use App\Application\Telegram\Conversation\Abstracts\BaseConversationAbstract;
use App\Application\Telegram\Conversation\Enums\ConversationTopic;
use App\Domain\MediaProcessing\Actions\ParseMediaAction;
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
        if ($message->text) {
            $resultMeasure = $message->text;
        } elseif ($message->photo) {
            $tempPath = storage_path('temp');
            $file = $this->telegram->downloadFile($message->photo[3]->file_id, $tempPath);
            $resultMeasure = app(ParseMediaAction::class)
                ->execute($file);
        } elseif($message->voice) {
            $tempPath = storage_path('temp');
            $file = $this->telegram->downloadFile($message->voice->file_id, $tempPath);
            $resultMeasure = app(ParseMediaAction::class)
                ->execute($file);
        } else {
            $this->reply(['text' => 'Я не умею работать с такими файлами, нужен текст, войс или фото']);
            return 'confirm';
        }

        app(SaveCheckupMeasurementsAction::class)->execute($this->checkup, $resultMeasure);

        $this->reply(['text' => 'Значение принято - ' . $resultMeasure]);

        return null;
    }

    public function getTopic(): ConversationTopic
    {
        return ConversationTopic::StartMeasurements;
    }
}
