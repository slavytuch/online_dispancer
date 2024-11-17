<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientCreateRequest;
use App\Http\Requests\PatientUpdateRequest;
use App\Http\Resources\PatientInfoResource;
use App\Http\Resources\PatientListItem;
use App\Models\Patient;

class PatientController extends Controller
{
    public function getById($patientId)
    {
        return PatientInfoResource::make(Patient::findOrFail($patientId));
    }

    public function getList()
    {
         return PatientListItem::collection(Patient::all());
    }

    public function update($patientId, PatientUpdateRequest $request)
    {
        Patient::findOrFail($patientId)->update($request->toArray());
    }

    public function add(PatientCreateRequest $request)
    {
        return Patient::create($request->toArray());
    }
}
