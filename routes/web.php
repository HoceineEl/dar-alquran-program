<?php

use App\Models\Ayah;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/quran-program'));

Route::get('/test', function () {
    // Fetch all ayahs grouped by page number
    $ayahsByPage = Ayah::all()->groupBy('page_number');

    // Calculate lines_count for each page and update the records
    foreach ($ayahsByPage as $pageNumber => $ayahs) {
        $maxLine = $ayahs->max('line_end');
        // Count the number of ayah_no = 1 on this page
        $ayah1Count = $ayahs->where('ayah_no', 1)->count();
        if ($ayah1Count > 0) {
            $maxLine -= (2 * $ayah1Count);
        }
        $linesCount = $maxLine;
        Ayah::where('page_number', $pageNumber)->update(['lines_count' => $linesCount]);
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
