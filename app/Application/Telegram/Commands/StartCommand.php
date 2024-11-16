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
            $patient = app(RegisterPatientAction::class)->execute($message->from->id, $message->from->first_name, $message->from->last_name);
            \Log::info('Пациент зарегистрирован', ['patient' => $patient->toArray()]);
        }

        $this->replyWithMessage([
            'text' => 'Успешно зарегистрирован',
            'reply_markup' => [
                Keyboard::button([
                    'text' => 'Поделиться контактами для дополнения данных',
                    'request_contact' => true,
                ]),
            ]
        ]);
    }
}
