<?php

namespace App\Domain\Patient\Actions;

use App\Domain\MediaProcessing\DTO\ProcessResult;
use App\Domain\MediaProcessing\ProcessingClient;
use App\Enums\PatientParamType;
use App\Models\Checkup;
use App\Models\PatientParamValue;

class SaveCheckupMeasurementsAction
{
    public function __construct(
        protected SucceedCheckupAction $action,
        protected ProcessingClient $processingClient
    ) {
    }

    public function execute(Checkup $checkup, ProcessResult $measurements)
    {
        $param = $checkup->patientParam;

        $value = match ($param->type) {
            PatientParamType::Float => floatval($measurements->value),
            PatientParamType::Integer => intval($measurements->value),
            PatientParamType::String => $measurements->value,
            PatientParamType::PressureLike => [
                'first' => explode('/', $measurements->value)[0],
                'second' => explode('/', $measurements->value)[1]
            ]
        };

        PatientParamValue::create([
            'patient_param_id' => $param->id,
            'patient_id' => $checkup->patient->id,
            'value' => $value
        ]);

        $value = [
            'value' => $value,
            'rawValue' => $measurements->rawValue,
            'description' => $measurements->description
        ];

        $this->action->execute($checkup, $value);
    }
}
