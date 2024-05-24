<?php

use App\Models\Ayah;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    $ayahs = Ayah::limit(80)->get();
    // i want to get the count of lines for each page using the last line number of each page 
    // but ther is some pages that has surah names or multiple ones so i will get the max line number for each page then calcuate the missing lines from this page between 1 and 15 then do 15 - max line number for each page

    foreach ($ayahs as $ayah) {
        $lines = Ayah::where('page_number', $ayah->page_number)->distinct('line_end')->pluck('line_end');
        $linesCount = $lines->count();
        $maxLine = $lines->max();
        $missing = $linesCount - $maxLine;
        dump($lines, $maxLine, $linesCount, $missing);
    }
});

use App\Models\Student;
use App\Services\WhatsAppService;
use Carbon\Carbon;




Route::get('/send-whatsapp', function () {
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

        $absentCount = $progresses->where('status', 'absent')->count();

        if ($absentCount == 1) {
            return $whatsAppService->sendMessage($student);
        }
        if ($absentCount == 2) {
            return $whatsAppService->sendMessage($student);
        }

        if ($absentCount >= 3) {
            return $whatsAppService->sendMessage($student);
        }
    }
});
