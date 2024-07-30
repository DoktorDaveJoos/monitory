<?php

namespace App\Jobs;

use App\Actions\PerformCheckAction;
use App\Actions\PerformCheckNotification;
use App\Actions\PerformCheckUpdate;
use App\Actions\PerformCheckValidation;
use App\DTOs\MonitorPassableDTO;
use App\Models\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
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
        $passable = MonitorPassableDTO::make(
            monitor: Monitor::find($this->monitorId)
        );

        $result = Pipeline::send($passable)
            ->through([
                PerformCheckAction::class,
                PerformCheckValidation::class,
                PerformCheckNotification::class,
                PerformCheckUpdate::class,
            ])
            ->thenReturn();

        $result->monitor->update(['last_checked_at' => now()]);

        Log::debug('Check performed', [
            'monitor_id' => $this->monitorId,
            'result' => $result,
        ]);
    }
}
