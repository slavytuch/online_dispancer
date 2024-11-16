<?php

namespace App\Models;

use App\Enums\PatientParamType;
use Illuminate\Database\Eloquent\Model;

class PatientParam extends Model
{
    protected $fillable = [
        'type',
        'name',
        'code',
    ];

    protected $casts = [
        'type' => PatientParamType::class
    ];

    public function values()
    {
        return $this->hasMany(PatientParamValue::class);
    }
}
