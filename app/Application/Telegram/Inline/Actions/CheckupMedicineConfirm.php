<?php

namespace App\Application\Telegram\Inline\Actions;

use App\Application\Telegram\Inline\Abstract\BaseInlineActionAbstract;
use App\Domain\Patient\Actions\SucceedCheckupAction;
use App\Enums\CheckupType;
use App\Models\Checkup;

class CheckupMedicineConfirm extends BaseInlineActionAbstract
{
    public function execute()
    {
        $parts = explode(':', $this->relatedObject->data);
        $checkupId = end($parts);

        $checkup = Checkup::find($checkupId);

        $this->telegram->editMessageText([
            'message_id' => $this->relatedObject->message->messageId,
            'chat_id' => $this->relatedObject->message->chat->id,
            'text' => 'Подтверждаю: ' . $checkup->description . ' - выполнено!'
        ]);

        $this->answer();

        app(SucceedCheckupAction::class)->execute($checkup);
    }
}
