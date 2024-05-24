<?php

use App\Models\Student;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    $whatsAppService = new WhatsAppService();
    $students = Student::with(['progress' => function ($query) {
        $query->where('date', '>=', Carbon::now()->subDays(3)->toDateString())
            ->orderBy('date', 'desc');
    }])->get();
    foreach ($students as $student) {
        $progresses = $student->progress;
        if ($progresses->isEmpty()) {
            continue;
        }

        return $whatsAppService->sendMessage($student->phone, $student->name);

        $absentCount = $progresses->where('status', 'absent')->count();

        if ($absentCount == 1) {
            $whatsAppService->sendMessage($student->phone, $student->name);
        }
        if ($absentCount == 2) {
            $whatsAppService->sendMessage($student->phone, $student->name);
        }

        if ($absentCount >= 3) {
            $whatsAppService->sendMessage($student->phone, $student->name);
        }
    }
})->everyMinute();
