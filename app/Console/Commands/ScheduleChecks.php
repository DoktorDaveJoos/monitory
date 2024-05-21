<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ScheduleChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitory:schedule-checks {frequency=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule checks for all active monitors';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {

        $monitors = DB::table('monitors')
            ->select('id')
            ->where('active', true)
            ->where('frequency', $this->argument('frequency'))
            ->get();

        $monitors->each(function ($monitor) {

        });

    }
}
