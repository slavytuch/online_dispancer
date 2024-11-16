<?php

namespace App\Domain\Patient\Actions;

use App\Models\Checkup;
use App\Models\PatientParamValue;

class SaveCheckupMeasurementsAction
{
    public function __construct(protected SucceedCheckupAction $action) {

    }

    public function execute(Checkup $checkup, mixed $measurements)
    {
        $param = $checkup->patient_param_id;
        //TODO: ВОТ ТУТ ПАРС ЗНАЧЕНИЙ ЧЕРЕЗ ИИ
        PatientParamValue::create([
            'patient_param_id' => $param->id,
            'user_id' => $checkup->patient->id,
            'value' => $measurements
        ]);
    }
}
