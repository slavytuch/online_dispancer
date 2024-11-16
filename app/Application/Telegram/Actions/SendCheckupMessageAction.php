<?php

namespace App\Application\Telegram\Actions;

use App\Application\Telegram\Inline\Enums\ActionFunction;
use App\Enums\CheckupType;
use App\Models\Checkup;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;

class SendCheckupMessageAction
{
    public function __construct(protected Api $telegram)
    {
    }

    public function execute(Checkup $checkup)
    {
        $keyboard = match ($checkup->type) {
            CheckupType::Medicine => [
                Keyboard::inlineButton(
                    [
                        'text' => 'Принял',
                        'callback_data' => ActionFunction::CheckupMedicineConfirm->value . $checkup->id
                    ]
                ),
            ],
            CheckupType::Measurements => [
                Keyboard::inlineButton(
                    [
                        'text' => 'Передаю показания',
                        'callback_data' => ActionFunction::CheckupMeasurementsConfirm->value . $checkup->id
                    ]
                )
            ]
        };

        $this->telegram->sendMessage([
            'chat_id' => $checkup->patient->telegram_id,
            'text' => $checkup->description,
            'reply_markup' => Keyboard::make(['inline_keyboard' => [$keyboard]])
        ]);
    }
}
