<?php

namespace App\Application\Telegram\Commands;

use App\Application\Telegram\Conversation\Actions\StartConversationAction;
use App\Application\Telegram\Conversation\ConversationHandlers\QuestionConversation;
use App\Application\Telegram\PatientHelper;
use Telegram\Bot\Commands\Command;

class AskQuestionCommand extends Command
{
    protected string $name = 'question';

    protected string $description = 'Задать вопрос врачу';

    public function handle()
    {
        app(StartConversationAction::class)->execute(
            PatientHelper::getByTelegramId($this->getUpdate()->getMessage()->from->id),
            new QuestionConversation($this->getTelegram())
        );
    }
}
