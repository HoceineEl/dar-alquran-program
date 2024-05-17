<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Progress;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ProgressSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('ar_SA');

        for ($i = 1; $i <= 10; $i++) {
            for ($j = 0; $j < 10; $j++) {
                Progress::create([
                    'member_id' => $i,
                    'date' => Carbon::now()->subDays($j)->toDateString(),
                    'status' => $faker->randomElement(['memorized', 'absent']),
                    'page' => $faker->numberBetween(1, 20),
                    'lines_from' => $faker->numberBetween(1, 15),
                    'lines_to' => $faker->numberBetween(16, 30),
                ]);
            }
        }
    }
}
