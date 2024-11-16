<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $firstUser = User::first();
        Patient::factory(5)
            ->create()
            ->each(static fn(Patient $patient) => $patient->doctor()->attach($firstUser));
    }
}
