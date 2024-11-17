<?php

namespace App\Application\Telegram\Commands;

use App\Application\Telegram\PatientHelper;
use App\Domain\Patient\Actions\RegisterPatientAction;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Button;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\WebApp\WebAppData;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Регистрирует пациента, ставит клавиатуру и команды';

    public function handle()
    {
        $message = $this->getUpdate()->getMessage();

        if (!$patient = PatientHelper::getByTelegramId($message->from->id)) {
            $patient = app(RegisterPatientAction::class)->execute(
                $message->from->id,
                $message->from->first_name,
                $message->from->last_name
            );
            \Log::info('Пациент зарегистрирован', ['patient' => $patient->toArray()]);
        }

        $this->replyWithMessage([
            'text' => 'Успешно зарегистрирован',
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

        if (!$patient->phone) {
            $this->replyWithMessage([
                'text' => 'Нужен номер телефона',
                'reply_markup' => Keyboard::forceReply([
                    'keyboard' => [[Keyboard::button(['text' => 'Вот номер телефона', 'request_contact' => true])]],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true,
                ])
            ]);
        }
    }
}
