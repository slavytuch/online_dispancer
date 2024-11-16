<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Dr ITTest',
            'email' => 'office@ittest-team.ru',
            'password' => bcrypt('123123123'),
        ]);
    }
}
