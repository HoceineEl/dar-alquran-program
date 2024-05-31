<?php

namespace App\Http\Controllers;

use App\Jobs\HandleAbsences;

abstract class Controller
{
    public function handleAbsences()
    {
        return response()->json(['message' => 'Absence handling and notifications triggered.']);
    }
}
