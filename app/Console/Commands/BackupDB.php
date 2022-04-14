<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class BackupDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make backup of your current database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //something...
        return 0;
    }
}
