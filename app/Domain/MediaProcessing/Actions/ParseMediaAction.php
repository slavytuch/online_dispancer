<?php

namespace App\Domain\MediaProcessing\Actions;

use App\Domain\MediaProcessing\ProcessingClient;
use App\Models\Checkup;

class ParseMediaAction
{
    public function __construct(protected ProcessingClient $processingClient)
    {
    }

    public function execute(string $media, Checkup $checkup)
    {
        return $this->processingClient->processFile($media, $checkup->patientParam);
    }
}
