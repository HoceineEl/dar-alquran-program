<?php

use App\Models\Student;
use App\Services\WhatsAppService;
use Illuminate\Console\Scheduling\Schedule as SchedulingSchedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (SchedulingSchedule $schedule) {
        $schedule->call(function () {
            $whatsAppService = new WhatsAppService();
            $student = Student::where('phone', '0697361188')->first();

            return $whatsAppService->sendMessage($student);
        })->everyMinute();
    })

    ->create();
