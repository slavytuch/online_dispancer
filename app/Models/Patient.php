<?php

namespace App\Models;

use App\Enums\Sex;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    protected $fillable = [
        'active',
        'sex',
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
        'disapncer_end' => 'datetime',
        'sex' => Sex::class
    ];

    public function paramValues()
    {
        return $this->hasMany(PatientParamValue::class);
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

    public function doctor()
    {
        return $this->belongsToMany(User::class);
    }
}
