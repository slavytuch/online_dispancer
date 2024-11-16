<?php

namespace App\Models;

use App\Enums\CheckupStatus;
use App\Enums\CheckupType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkup extends Model
{
    use HasFactory;
    protected $fillable = [
        'patient_id',
        'type',
        'checkup_data',
        'status',
        'start_at',
        'deadline',
        'try',
        'description',
        'patient_param_id'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'try' => 'integer',
        'status' => CheckupStatus::class,
        'checkup_data' => 'array',
        'type' => CheckupType::class
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function param()
    {
        return $this->belongsTo(PatientParam::class);
    }
}
