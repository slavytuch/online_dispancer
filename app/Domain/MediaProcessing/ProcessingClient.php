<?php

namespace App\Domain\MediaProcessing;

use App\Domain\MediaProcessing\DTO\ProcessResult;
use App\Enums\PatientParamType;
use App\Models\PatientParam;
use Illuminate\Support\Facades\Http;

class ProcessingClient
{
    protected string $url;

    public function __construct()
    {
        $this->url = config('mediaprocessing.url');
    }

    public function transcribe($filepath)
    {
        return Http::attach('file', file_get_contents($filepath), basename($filepath))->post(
            $this->url,
            ['message' => 'Перевести из файла в текст']
        );
    }

    public function processFile(string $filepath, PatientParam $param): ProcessResult
    {
        $format = match ($param->type) {
            PatientParamType::PressureLike => 'Число/Число',
            PatientParamType::String => 'Строка',
            PatientParamType::Float, PatientParamType::Integer => 'Число',
        };

        $response = Http::attach('file', file_get_contents($filepath), basename($filepath))->post(
            $this->url,
            ['message' => 'Параметр "' . $param->name . '" в формате "' . $format . '"']
        );

        $value = null;

        switch ($param->code) {
            case 'pressure':
                $matches = [];
                if (preg_match('/\d+\/\d+/', $response['value'], $matches)) {
                    $numbers = explode('/', $matches[0]);
                    $value = [
                        'first' => $numbers[0],
                        'second' => $numbers[1]
                    ];
                }
                break;
            case 'weight':
                $matches = [];
                if (preg_match('/(\d+) кг/', $response['value'], $matches)) {
                    $value = $matches[1];
                }
                break;
            default:
                $value = $response['value'];
        }

        return new ProcessResult(
            value: $value,
            rawValue: $response['value'],
            description: $response['description']
        );
    }
}
