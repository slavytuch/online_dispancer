<?php

namespace App\Application\Telegram;

use App\Models\Patient;

class PatientHelper
{
    public static function getByTelegramId(string $telegramId): ?Patient
    {
        return Patient::where('telegram_id', $telegramId)->first();
    }
}
