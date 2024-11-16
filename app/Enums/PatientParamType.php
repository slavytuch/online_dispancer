<?php

namespace App\Enums;

enum PatientParamType: string
{
    case Integer = 'integer';
    case Float = 'float';
    case String = 'string';
    case PressureLike = 'pressure-like';
}
