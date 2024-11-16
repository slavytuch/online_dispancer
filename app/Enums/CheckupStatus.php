<?php

namespace App\Enums;

enum CheckupStatus: string
{
    case NotStarted = 'not-started';
    case InProgress = 'in-progress';
    case Finished = 'finished';
    case Fail = 'fail';
}
