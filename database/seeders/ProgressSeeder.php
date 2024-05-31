<?php

namespace Database\Seeders;

use App\Classes\Core;
use App\Helpers\ProgressFormHelper;
use App\Models\Progress;
use App\Models\Student;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ProgressSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('ar_SA');
        $students = Student::all();

        foreach ($students as $student) {
            $pageData = ProgressFormHelper::calculateNextProgress($student);
            for ($j = 0; $j < 10; $j++) {
                Progress::create([
                    'student_id' => $student->id,
                    'date' => Carbon::now()->subDays($j)->toDateString(),
                    'status' => $faker->randomElement(['memorized', 'absent']),
                    'ayah' => $faker->numberBetween(1, 20),
                    'page_id' => $pageData['page_id'] ?? $j + 1,
                    'lines_from' => $pageData['lines_from'],
                    'lines_to' => $pageData['lines_to'],
                ]);
            }
        }
    }
}
