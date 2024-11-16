<?php

namespace App\Application\Telegram;

use App\Application\Telegram\Inline\Actions\CheckupMeasurementsStart;
use App\Application\Telegram\Inline\Actions\CheckupMedicineConfirm;
use App\Application\Telegram\Inline\Enums\ActionFunction;
use App\Application\Telegram\Inline\InlineActionInterface;
use App\Models\Patient;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\CallbackQuery;

class InlineActionFactory
{
    protected Patient $user;

    protected array $actionProcedureRegistry = [

    ];

    protected array $actionFunctionRegistry = [
        ActionFunction::CheckupMedicineConfirm->value => CheckupMedicineConfirm::class,
        ActionFunction::CheckupMeasurementsConfirm->value => CheckupMeasurementsStart::class,
    ];

    public function __construct(protected Api $telegram, protected CallbackQuery $relatedObject)
    {
        $this->user = PatientHelper::getByTelegramId($this->relatedObject->from->id);
    }

    public function getAction(): ?InlineActionInterface
    {
        try {
            $className = $this->getActionClassName();
            return new $className ($this->telegram, $this->relatedObject, $this->user);
        } catch (\Exception) {
            $this->telegram->answerCallbackQuery(['text' => 'Кнопка не работает - не знаю что делать']);
            return null;
        }
    }

    protected function getActionClassName(): string
    {
        $data = $this->relatedObject->data;

        foreach ($this->actionProcedureRegistry as $code => $handler) {
            if ($code === $data) {
                return $handler;
            }
        }

        foreach ($this->actionFunctionRegistry as $code => $handler) {
            if (str_starts_with($data, $code)) {
                return $handler;
            }
        }

        throw new \Exception('Действие не определено');
    }
}
