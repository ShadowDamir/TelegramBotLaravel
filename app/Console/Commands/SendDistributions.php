<?php

namespace App\Console\Commands;

use App\Http\Controllers\TelegramController;
use Illuminate\Console\Command;

class SendDistributions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'distributions:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send distributions in chats';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $result = TelegramController::Distribution();
    }
}
