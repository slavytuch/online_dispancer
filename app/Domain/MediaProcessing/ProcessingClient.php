<?php

namespace App\Domain\MediaProcessing;

use App\Models\PatientParam;
use Illuminate\Support\Facades\Http;

class ProcessingClient
{
    protected string $url;
    public function __construct()
    {
        $this->url = config('mediaprocessing.url');
    }

    public function processFile(string $filepath, PatientParam $param): string
    {
        \Log::info('filepath', ['path' => $filepath]);
        $response = Http::attach('file', file_get_contents($filepath), basename($filepath))->post(
            $this->url,
            ['message' => 'Параметр ' . $param->name]
        );
        \Log::info('response', ['response' => $response]);

        return $response['message'];
    }
}
