<?php

namespace Database\Seeders;

use App\Models\Designation;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $designations = [
            'CEO',
            'Backend Developer',
            'Frontend Developer',
            'Digital Marketer',
        ];

        foreach ($designations as $designation) {
            Designation::create(['title' => $designation]);
        }

        User::create([
            'designation_id' => 1,
            'first_name' => 'Sahil',
            'last_name' => 'Chahal',
            'email' => 'dev.sahilchahal1@gmail.com',
            'password' => \Hash::make('Sahil@123'),
        ]);
    }
}
