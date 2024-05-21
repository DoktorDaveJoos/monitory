<?php

namespace App\Jobs;

use App\Actions\MonitorStrategies\HttpMonitorStrategy;
use App\Actions\PerformCheckAction;
use App\Models\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Pipeline;

class PerformCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        readonly int $monitorId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        // 1. Get the monitor
        $monitor = Monitor::find($this->monitorId);

        Pipeline::send($monitor)
            ->through([
                PerformCheckAction::class
            ])
            ->thenReturn();



        // 2. Create a new HttpMonitorStrategy
        $strategy = HttpMonitorStrategy::make(
            $monitor->toArray()
        );

        // 3. Check the monitor
        $strategy->check();

        // 4. Dispatch a new CheckCompleted event
    }
}
