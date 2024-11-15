<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checkup extends Model
{
    protected $fillable = [
        'patient_id',
        'type',
        'checkup_data',
        'status',
        'start_at',
        'deadline',
        'try'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'try' => 'integer'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
