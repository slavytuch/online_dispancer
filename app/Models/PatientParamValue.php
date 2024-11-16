<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientParamValue extends Model
{
    protected $fillable = [
        'patient_param_id',
        'patient_id',
        'value',
    ];

    protected $casts = [
        'value' => 'array'
    ];

    public function patientParam()
    {
        return $this->belongsTo(PatientParam::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
