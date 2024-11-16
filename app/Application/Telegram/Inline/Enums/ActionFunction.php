<?php

namespace App\Application\Telegram\Inline\Enums;

enum ActionFunction: string
{
    case CheckupMedicineConfirm = 'checkup:medicine:confirm:';
    case CheckupMeasurementsConfirm = 'checkup:measurements:confirm:';
}
