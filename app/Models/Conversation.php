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
        'next_stage'
    ];

    protected $casts = [
        'finished' => 'boolean',
        'topic' => ConversationTopic::class
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
