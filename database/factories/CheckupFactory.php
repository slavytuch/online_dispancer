<?php

namespace Database\Factories;

use App\Enums\CheckupStatus;
use App\Enums\CheckupType;
use App\Models\Patient;
use App\Models\PatientParam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Checkup>
 */
class CheckupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = CheckupType::cases()[array_rand(CheckupType::cases())];
        $status = CheckupStatus::cases()[array_rand(CheckupStatus::cases())];
        $param = PatientParam::inRandomOrder()->first();
        $wellBeings = [
            'Хорошее',
            'Плохое',
            'Нейтральное',
        ];
        return [
            'patient_id' => Patient::inRandomOrder()->first()->id,
            'type' => $type,
            'status' => $status,
            'description' => match ($type) {
                CheckupType::Medicine => 'Принять "' . $this->faker->word . '" 1 табл',
                CheckupType::Measurements => 'Передать показания по параметру "' . $param->name . '"'
            },
            'checkup_data' => ['value' => match ($status) {
                default => [],
                CheckupStatus::Finished => match ($type) {
                    CheckupType::Medicine => ['success' => true],
                    CheckupType::Measurements => match ($param->code) {
                        'weight' => $this->faker->numberBetween(50, 200),
                        'temperature' => $this->faker->randomFloat(1, 34, 39),
                        'sugar' => $this->faker->randomFloat(1, 3.5, 12),
                        'well-being' => $wellBeings[array_rand($wellBeings)],
                        'pressure' => implode(
                            '/',
                            [$this->faker->numberBetween(90, 200), $this->faker->numberBetween(60, 120)]
                        )
                    }
                }
            }],
            'start_at' => match ($status) {
                CheckupStatus::NotStarted => now()->add('30 minutes'),
                CheckupStatus::Finished, CheckupStatus::Fail => now()->add('-30 minutes'),
                CheckupStatus::InProgress => now()->add('5 minutes'),
            },
            'deadline' => match ($status) {
                CheckupStatus::NotStarted => now()->add('1 hour'),
                CheckupStatus::Finished, CheckupStatus::Fail => now()->add('-15 minutes'),
                CheckupStatus::InProgress => now()->add('25 minutes'),
            },
            'try' => match ($status) {
                CheckupStatus::NotStarted => 0,
                CheckupStatus::Fail => 5,
                CheckupStatus::Finished, CheckupStatus::InProgress => $this->faker->numberBetween(1, 4),
            },
            'patient_param_id' => match ($type) {
                CheckupType::Medicine => null,
                CheckupType::Measurements => $param->id
            }
        ];
    }
}
