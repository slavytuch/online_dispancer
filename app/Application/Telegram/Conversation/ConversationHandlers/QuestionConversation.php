<?php

namespace App\Application\Telegram\Conversation\ConversationHandlers;

use App\Application\Telegram\Conversation\Abstracts\BaseConversationAbstract;
use App\Application\Telegram\Conversation\Enums\ConversationTopic;
use App\Application\Telegram\PatientHelper;
use App\Domain\MediaProcessing\ProcessingClient;
use App\Models\Question;

class QuestionConversation extends BaseConversationAbstract
{

    public function init(): ?string
    {
        $this->reply([
            'text' => 'Какой вопрос вы хотите задать врачу?'
        ]);

        return 'confirm';
    }

    public function confirm()
    {
        $message = $this->telegram->getWebhookUpdate()->getMessage();

        $patient = PatientHelper::getByTelegramId($message->from->id);

        if ($message->text) {
            $result['transcription'] = $message->text;
        } elseif ($message->photo) {
            $tempPath = storage_path('temp');
            $file = $this->telegram->downloadFile($message->photo[3]->file_id, $tempPath);
            $result = app(ProcessingClient::class)
                ->transcribe($file);
        } elseif ($message->voice) {
            $tempPath = storage_path('temp');
            $file = $this->telegram->downloadFile($message->voice->file_id, $tempPath);
            $result = app(ProcessingClient::class)
                ->transcribe($file);
        } else {
            $this->reply(['text' => 'Я не умею работать с такими файлами, нужен текст, голосовое сообщение или фото']);
            return 'confirm';
        }

        $questionText =  $result['transcription'] ?? 'Не смог понять';
        Question::create([
            'patient_id' => $patient->id,
            'user_id' => $patient->doctor->first()->id,
            'question_text' => $questionText,
            'read' => false
        ]);

        $this->reply([
                'text' => 'Принял, как доктор ответит - я тебе пришлю ответ.' .PHP_EOL . 'Твой вопрос:' .PHP_EOL . $questionText
            ]
        );

        return null;
    }

    public function getTopic(): ConversationTopic
    {
        return ConversationTopic::SendQuestion;
    }
}
