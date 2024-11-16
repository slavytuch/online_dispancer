<?php

namespace Database\Seeders;

use App\Enums\PatientParamType;
use App\Models\PatientParam;
use Illuminate\Database\Seeder;

class PatientParamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PatientParam::create([
            'name' => 'Сахар',
            'code' => 'sugar',
            'type' => PatientParamType::Float,
        ]);
        PatientParam::create([
            'name' => 'Давление',
            'code' => 'pressure',
            'type' => PatientParamType::PressureLike,
        ]);
        PatientParam::create([
            'name' => 'Самочувствие',
            'code' => 'well-being',
            'type' => PatientParamType::String,
        ]);
        PatientParam::create([
            'name' => 'Температура',
            'code' => 'temperature',
            'type' => PatientParamType::Float,
        ]);
        PatientParam::create([
            'name' => 'Вес',
            'code' => 'weight',
            'type' => PatientParamType::Integer,
        ]);
    }
}
