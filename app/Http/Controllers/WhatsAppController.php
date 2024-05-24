<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function sendWhatsAppMessage(Request $request)
    {
        $to = '+212697361188';
        $studentName = 'Hoceine el idrissi';

        return $this->whatsAppService->sendMessage($to, $studentName);
    }
}
