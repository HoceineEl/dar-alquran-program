<?php

namespace Database\Seeders;

use App\Models\Progress;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ProgressSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('ar_SA');

        // for ($i = 1; $i <= 4; $i++) {
        //     for ($j = 0; $j < 1; $j++) {
        //         Progress::create([
        //             'student_id' => $i,
        //             'date' => Carbon::now()->subDays($j)->toDateString(),
        //             'status' => $faker->randomElement(['memorized', 'absent']),
        //             'ayah' => $faker->numberBetween(1, 20),
        //             'lines_from' => $faker->numberBetween(1, 15),
        //             'lines_to' => $faker->numberBetween(16, 30),
        //         ]);
        //     }
        // }
    }
}
