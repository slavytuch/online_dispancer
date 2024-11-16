<?php

namespace App\Domain\Patient\Actions;

use App\Models\Patient;

class RegisterPatientAction
{
    public function execute($telegramId, $firstName, $lastName)
    {
        return Patient::factory(1, [
            'telegram_id' => $telegramId,
            'name' => $firstName,
            'last_name' => $lastName
        ])->create()->first;
    }
}
