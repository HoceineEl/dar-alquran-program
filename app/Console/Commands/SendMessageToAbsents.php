<?php

namespace App\Console\Commands;

use App\Classes\Core;
use Illuminate\Console\Command;

class SendMessageToAbsents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-message-to-absence';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp messages to students with absences';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Core::sendMessageToAbsence();
        $this->info('Messages sent to absent students.');
    }
}
