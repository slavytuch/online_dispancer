<?php

namespace App\Domain\Patient\Actions;

use App\Domain\MediaProcessing\ProcessingClient;
use App\Models\Checkup;
use App\Models\PatientParamValue;

class SaveCheckupMeasurementsAction
{
    public function __construct(protected SucceedCheckupAction $action, protected ProcessingClient $processingClient)
    {
    }

    public function execute(Checkup $checkup, mixed $measurements)
    {
        $param = $checkup->patientParam;

        PatientParamValue::create([
            'patient_param_id' => $param->id,
            'patient_id' => $checkup->patient->id,
            'value' => $measurements
        ]);

        $this->action->execute($checkup);
    }
}
