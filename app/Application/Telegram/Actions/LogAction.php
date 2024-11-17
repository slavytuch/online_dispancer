<?php

namespace App\Application\Telegram\Actions;

use App\Models\ConversationLog;

class LogAction
{
    public function execute($telegramId, $messageId, $data)
    {
        ConversationLog::create([
            'telegram_id' => $telegramId,
            'message_id' => $messageId,
            'data' => $data
        ]);
    }
}
