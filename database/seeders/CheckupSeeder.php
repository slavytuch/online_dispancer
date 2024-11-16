<?php

namespace Database\Seeders;

use App\Models\Checkup;
use Illuminate\Database\Seeder;

class CheckupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Checkup::factory(100)->create();
    }
}
