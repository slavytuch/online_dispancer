<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientParam extends Model
{
    protected $fillable = [
        'type',
        'name',
        'code',
        'multiple',
    ];

    protected $casts = [
        'multiple' => 'boolean'
    ];
}
