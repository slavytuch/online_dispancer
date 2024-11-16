<?php

namespace App\Models;

use App\Application\Telegram\Conversation\Enums\ConversationTopic;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'patient_id',
        'message_id',
        'topic',
        'finished',
        'next_stage',
        'data'
    ];

    protected $casts = [
        'finished' => 'boolean',
        'topic' => ConversationTopic::class,
        'data' => 'array'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
