<?php

namespace App\Domain\Patient\Actions;

use App\Domain\Helpers\PatientParamValueHelper;
use App\Models\Checkup;
use App\Models\PatientParamValue;

class SaveCheckupMeasurementsAction
{
    public function __construct(protected SucceedCheckupAction $action, ) {

    }

    public function execute(Checkup $checkup, mixed $measurements)
    {
        $resultMeasure = null;
        if($measurements->text) {
            $resultMeasure = $measurements->text;
        } elseif ($measurements->photo) {
           $resultMeasure = PatientParamValueHelper::parsePhoto($measurements->photo);
        }

        $param = $checkup->patientParam;

        PatientParamValue::create([
            'patient_param_id' => $param->id,
            'patient_id' => $checkup->patient->id,
            'value' => $resultMeasure
        ]);

        $this->action->execute($checkup);

        return $resultMeasure;
    }
}
