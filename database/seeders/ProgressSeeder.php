<?php

namespace Database\Seeders;

use App\Helpers\ProgressFormHelper;
use App\Models\Progress;
use App\Models\Student;
use App\Models\User;
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
            $managers = $student->group->managers->pluck('id')->toArray();

            for ($j = 0; $j < 10; $j++) {
                $student->progresses()->create([
                    'date' => Carbon::now()->subDays($j)->toDateString(),
                    'status' => $faker->randomElement(['memorized', 'absent']),
                    'page_id' => $pageData['page_id'] ?? $j + 1,
                    'comment' => $faker->sentence,
                    'lines_from' => $pageData['lines_from'],
                    'lines_to' => $pageData['lines_to'],
                    'notes' => $faker->sentence,
                ])->createdBy()->associate(User::find($faker->randomElement($managers)))->save();
            }
        }
    }
}
