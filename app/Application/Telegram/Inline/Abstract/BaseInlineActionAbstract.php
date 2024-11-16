<?php

namespace App\Application\Telegram\Inline\Abstract;

use App\Application\Telegram\Inline\InlineActionInterface;
use App\Models\Patient;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\CallbackQuery;

abstract class BaseInlineActionAbstract implements InlineActionInterface
{
    public function __construct(protected Api $telegram, protected CallbackQuery $relatedObject, protected Patient $user)
    {
    }


    protected function answer(?string $text = null, bool $showAlert = false): void
    {
        $this->telegram->answerCallbackQuery([
            'callback_query_id' => $this->relatedObject->id,
            'text' => $text,
            'show_alert' => $showAlert
        ]);
    }
}
