<?php

namespace App\Console\Commands;

use App\Jobs\SendCheckupToUser;
use Illuminate\Console\Command;

class RefreshCheckups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-checkups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(SendCheckupToUser $job)
    {
        $job->handle();
    }
}
