<?php

namespace App\Application\Telegram\Actions;

use App\Models\Question;
use Telegram\Bot\Api;

class SendPatientAnswerAction
{
    public function __construct(protected Api $telegram)
    {
    }


    public function execute(Question $question)
    {
        $this->telegram->sendMessage([
            'chat_id' => $question->patient->telegram_id,
            'text' => 'Добрый день!' . PHP_EOL .
                'По вопросу есть ответ:<blockquote>' . $question->question_text . '</blockquote>' . PHP_EOL .
                $question->answer_text,
            'parse_mode' => 'HTML'
        ]);
    }
}
