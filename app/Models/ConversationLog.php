<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationLog extends Model
{
    protected $fillable = [
        'telegram_id',
        'message_id',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];
}
