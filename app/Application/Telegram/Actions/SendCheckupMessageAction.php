<?php

namespace App\Application\Telegram\Actions;

use App\Application\Telegram\Conversation\Actions\StartConversationAction;
use App\Application\Telegram\Conversation\ConversationHandlers\MeasurementsConversation;
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
        $keyboard = [];
        switch ($checkup->type) {
            case CheckupType::Medicine:
                $keyboard = [
                    Keyboard::inlineButton(
                        [
                            'text' => 'Принял',
                            'callback_data' => ActionFunction::CheckupMedicineConfirm->value . $checkup->id
                        ]
                    ),
                ];
                break;
            case CheckupType::Measurements:
                $keyboard = [
                    Keyboard::inlineButton(
                        [
                            'text' => 'Передаю показания',
                            'callback_data' => ActionFunction::CheckupMeasurementsConfirm->value . $checkup->id
                        ]
                    )
                ];
                break;
        }

        $this->telegram->sendMessage([
            'chat_id' => $checkup->patient->telegram_id,
            'text' => $checkup->description,
            'reply_markup' => Keyboard::make(['inline_keyboard' => [$keyboard]])
        ]);
    }
}
