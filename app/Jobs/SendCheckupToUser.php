<?php

namespace App\Jobs;

use App\Domain\Patient\Actions\NotifyPatientAction;
use App\Domain\Patient\Actions\RotatePendingCheckupsAction;
use App\Models\Checkup;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendCheckupToUser implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected RotatePendingCheckupsAction $rotatePendingCheckupsAction,
        protected NotifyPatientAction $notifyPatientAction
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /**
         * @var Checkup $pendingCheckup
         */
        foreach ($this->rotatePendingCheckupsAction->execute() as $pendingCheckup) {

            $this->notifyPatientAction->execute($pendingCheckup);
        }
    }
}
