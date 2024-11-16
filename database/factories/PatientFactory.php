<?php

namespace Database\Factories;

use App\Enums\Sex;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $reasons = [
            'Сахарный диабет',
            'Аритмия',
            'Атеросклероз',
            'Гипертония',
            'Хроническая обструктивная болезнь лёгких',
        ];
        return [
            'sex' => Sex::cases()[array_rand(Sex::cases())],
            'name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'height' => $this->faker->numberBetween(150, 220),
            'weight' => $this->faker->numberBetween(50, 200),
            'dispancer_reason' => $reasons[array_rand($reasons)],
            'dispancer_start' => now(),
            'dispancer_end' => now()->add('2 weeks'),
        ];
    }
}
