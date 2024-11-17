<?php

namespace App\Application\Telegram\Commands;

use App\Application\Telegram\PatientHelper;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class WebAppButtonCommand extends Command
{
    protected string $name = 'webappbutton';
    protected string $description = 'Отправляет сообщение с ссылкой на вебапп';

    public function handle()
    {
        Log::info('from', ['from' => $this->getUpdate()->getRelatedObject()->from->id]);
        $this->replyWithMessage([
            'text' => 'Кнопка на вебапп',
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [
                    [
                        Keyboard::inlineButton([
                            'text' => 'Личный кабинет',
                            'web_app' => [
                                'url' => config('telegram.webapp_url') . '?patientId=' . PatientHelper::getByTelegramId(
                                        $this->getUpdate()->getRelatedObject()->from->id
                                    )->id
                            ]
                        ])
                    ]
                ]
            ])
        ]);
    }
}
