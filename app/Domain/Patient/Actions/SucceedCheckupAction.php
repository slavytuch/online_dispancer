<?php

namespace App\Domain\Patient\Actions;

use App\Enums\CheckupStatus;
use App\Models\Checkup;

class SucceedCheckupAction
{
    public function execute(Checkup $checkup, $result = null)
    {
        $checkup->status = CheckupStatus::Finished;
        $checkup->checkup_data = $result;
        $checkup->save();
    }
}
