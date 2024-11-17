<?php

namespace App\Application\Telegram\Conversation\ConversationHandlers;

use App\Application\Telegram\Conversation\Abstracts\BaseConversationAbstract;
use App\Application\Telegram\Conversation\Enums\ConversationTopic;
use App\Domain\MediaProcessing\Actions\ParseMediaAction;
use App\Domain\MediaProcessing\DTO\ProcessResult;
use App\Domain\Patient\Actions\SaveCheckupMeasurementsAction;
use App\Models\Checkup;
use Illuminate\Support\Facades\Log;

class MeasurementsConversation extends BaseConversationAbstract
{
    protected Checkup $checkup;

    public function setCheckup(Checkup $checkup)
    {
        $this->checkup = $checkup;
    }

    public function init(): ?string
    {
        return 'confirm';
    }

    public function confirm()
    {
        $message = $this->telegram->getWebhookUpdate()->getMessage();

        Log::info('Confirm', ['message' => $message]);
        if ($message->text) {
            $resultMeasure = new ProcessResult(
                value: $message->text,rawValue: $message->text, description: 'Прямое сообщение, не обработано'
            );
        } elseif ($message->photo) {
            $tempPath = storage_path('temp');
            $file = $this->telegram->downloadFile($message->photo[3]->file_id, $tempPath);
            $resultMeasure = app(ParseMediaAction::class)
                ->execute($file, $this->checkup);
        } elseif ($message->voice) {
            $tempPath = storage_path('temp');
            $file = $this->telegram->downloadFile($message->voice->file_id, $tempPath);
            $resultMeasure = app(ParseMediaAction::class)
                ->execute($file, $this->checkup);
        } else {
            $this->reply(['text' => 'Я не умею работать с такими файлами, нужен текст, голосовое сообщение или фото']);
            return 'confirm';
        }

        app(SaveCheckupMeasurementsAction::class)->execute($this->checkup, $resultMeasure);

        $this->reply(
            ['text' => 'Значение параметра "' . $this->checkup->patientParam->name . '" принято - ' . $resultMeasure]
        );

        return null;
    }

    public function getTopic(): ConversationTopic
    {
        return ConversationTopic::StartMeasurements;
    }
}
