<?php

namespace App\Domain\Patient\Actions;

use App\Enums\CheckupStatus;
use App\Models\Checkup;

class SucceedCheckupAction
{
    public function execute(Checkup $checkup)
    {
        $checkup->status = CheckupStatus::Finished;
        $checkup->save();
    }
}
