<?php

namespace App\Domain\Patient\Actions;

use App\Application\Telegram\Actions\SendCheckupMessageAction;
use App\Models\Checkup;

class NotifyPatientAction
{
    public function __construct(protected SendCheckupMessageAction $sendCheckupMessageAction)
    {
    }

    public function execute(Checkup $checkup)
    {
        if (!$checkup->patient->telegram_id) {
            return;
        }

        $this->sendCheckupMessageAction->execute($checkup);
    }
}
