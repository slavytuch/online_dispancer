<?php

namespace App\Domain\MediaProcessing\DTO;

use Spatie\LaravelData\Data;

class ProcessResult extends Data
{
    public function __construct(
        public readonly mixed $value,
        public readonly mixed $rawValue,
        public readonly string $description
    ) {
    }
}
