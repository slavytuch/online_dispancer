<?php

namespace App\Http\Controllers;

use App\Http\Resources\PatientInfoResource;
use App\Models\Patient;

class PatientController extends Controller
{
    public function getById($patientId)
    {
        return PatientInfoResource::make(Patient::findOrFail($patientId));
    }
}
