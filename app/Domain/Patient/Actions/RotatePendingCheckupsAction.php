<?php

namespace App\Domain\Patient\Actions;

use App\Enums\CheckupStatus;
use App\Models\Checkup;

class RotatePendingCheckupsAction
{
    public function execute()
    {
        $result = [];
        foreach (
            Checkup::whereIn('status', [CheckupStatus::NotStarted, CheckupStatus::InProgress])
                ->where('start_at', '>=', now())
                ->get() as $pendingCheckup
        ) {
            if ($pendingCheckup->try >= 5 || $pendingCheckup->deadline > now()) {
                $pendingCheckup->status = CheckupStatus::Fail;
                //TODO: Выплюнуть событие
                continue;
            }

            if ($pendingCheckup->status === CheckupStatus::NotStarted) {
                $pendingCheckup->status = CheckupStatus::InProgress;
            }

            $pendingCheckup->try++;
            $pendingCheckup->save();

            $result[] = $pendingCheckup;
        }

        return $result;
    }
}
