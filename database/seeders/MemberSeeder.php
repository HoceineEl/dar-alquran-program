<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use Faker\Factory as Faker;

class MemberSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('ar_SA');

        for ($i = 0; $i < 10; $i++) {
            Member::create([
                'name' => $faker->name,
                'type' => $faker->randomElement(['two_lines', 'half_page']),
                'phone' => $faker->phoneNumber,
                'group' => '1',
                'sex' => 'male',
            ]);
        }
    }
}
