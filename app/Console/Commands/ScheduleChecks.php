<?php

namespace App\Console\Commands;

use App\Jobs\PerformCheck;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScheduleChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitory:schedule-checks {interval=5}';

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
            ->where('interval', $this->argument('interval'))
            ->get();

        $monitors->each(function ($monitor) {
            Log::debug('Dispatching PerformCheck job', ['monitor_id' => $monitor->id]);
            PerformCheck::dispatch($monitor->id);
        });

    }
}
