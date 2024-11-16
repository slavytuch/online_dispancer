<?php

namespace App\Http\Resources;

use App\Models\Checkup;
use App\Models\Patient;
use App\Models\PatientParamValue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Patient $resource
 */
class PatientInfoResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...$this->resource->toArray(),
            'prescriptions' => $this->resource->prescriptions()->get(),
            'doctor' => $this->resource->doctor()->first(),
            'checkups' => $this->resource->checkups()->get()->map(static fn(Checkup $checkup) => [
                ...$checkup->toArray(),
                'formattedValue' => $checkup->formatValue()
            ]),
            'params' => $this->resource->paramValues()->get()->map(function (PatientParamValue $value) {
                $param = $value->patientParam;
                return [
                    'created_at' => $value->created_at,
                    'name' => $param->name,
                    'value' => $value->value,
                    'code' => $param->code,
                    'type' => $param->type
                ];
            })
        ];
    }
}
