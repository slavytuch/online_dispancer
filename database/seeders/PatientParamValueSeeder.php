<?php

namespace Database\Seeders;

use App\Domain\Helpers\PatientParamValueHelper;
use App\Models\Patient;
use App\Models\PatientParam;
use Illuminate\Database\Seeder;

class PatientParamValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $params = PatientParam::all();
        $patients = Patient::all();

        $faker = fake();
        $wellBeings = [
            'Хорошее',
            'Плохое',
            'Нейтральное',
        ];
        foreach ($params as $param) {
            foreach ($patients as $patient) {
                PatientParamValueHelper::set(
                    $patient,
                    $param,
                    match ($param->code) {
                        'weight' => $faker->numberBetween(50, 200),
                        'temperature' => $faker->randomFloat(1, 34, 39),
                        'sugar' => $faker->randomFloat(1, 3.5, 12),
                        'well-being' => $wellBeings[array_rand($wellBeings)],
                        'pressure' => implode('/', [$faker->numberBetween(90, 200), $faker->numberBetween(60, 120)])
                    }
                );
            }
        }
    }
}
