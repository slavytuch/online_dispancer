<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'active',
        'name',
        'last_name',
        'patronymic',
        'telegram_id',
        'photo',
        'height',
        'weight',
        'dispancer_reason',
        'dispancer_start',
        'dispancer_end',
    ];

    protected $casts = [
        'active' => 'bool',
        'telegram_id' => 'int',
        'height' => 'float',
        'weight' => 'float',
        'dispancer_start' => 'datetime',
        'disapncer_end' => 'datetime'
    ];

    public function params()
    {
        return $this->belongsToMany(PatientParam::class);
    }

    public function checkups()
    {
        return $this->hasMany(Checkup::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
