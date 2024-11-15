<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'patient_id',
        'message_id',
        'topic',
        'finished'
    ];

    protected $casts = [
        'finished' => 'boolean'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
