<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use Faker\Factory as Faker;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('ar_SA');

        for ($i = 0; $i < 10; $i++) {
            Student::create([
                'name' => $faker->name,
                'type' => $faker->randomElement(['two_lines', 'half_page']),
                'phone' => $faker->phoneNumber,
                'group' => '1',
                'sex' => 'male',
            ]);
        }
    }
}
