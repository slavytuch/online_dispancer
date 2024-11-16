<?php

namespace App\Domain\Helpers;


use App\Enums\PatientParamType;
use App\Models\Patient;
use App\Models\PatientParam;
use App\Models\PatientParamValue;

class PatientParamValueHelper
{
    public static function set(Patient $patient, PatientParam $param, string $value)
    {
        PatientParamValue::create([
            'patient_id' => $patient->id,
            'patient_param_id' => $param->id,
            'value' => match ($param->type) {
                PatientParamType::Integer, PatientParamType::Float, PatientParamType::String => $value,
                PatientParamType::PressureLike => [
                    'first' => explode('/', $value)[0],
                    'second' => explode('/', $value)[1]
                ]
            }
        ]);
    }
}
