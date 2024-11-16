<?php

namespace App\Domain\MediaProcessing\Actions;

use App\Domain\MediaProcessing\ProcessingClient;

class ParseMediaAction
{
    public function __construct(protected ProcessingClient $processingClient)
    {
    }

    public function execute($media)
    {
        return $this->processingClient->processFile($media);
    }
}
