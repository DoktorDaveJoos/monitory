<?php

namespace App\Actions;

use App\DTOs\MonitorPassableDTO;
use App\Notifications\MonitorRecovered;
use App\Notifications\TriggerAlert;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class PerformCheckNotification
{
    use AsAction;

    /**
     * @throws Throwable
     */
    public function handle(MonitorPassableDTO $monitorPassableDTO, Closure $next)
    {
        Log::debug('Performing validation for check', [
            'monitor_id' => $monitorPassableDTO->monitor->id,
            'monitor_type' => $monitorPassableDTO->monitor->type->value,
        ]);

        $healthy = $monitorPassableDTO->monitor->success;
        $failedCheck = $monitorPassableDTO->failed();

        // Only send alert if monitor is not already in failed state
        if ($healthy && $failedCheck) {
            $this->sendAlert($monitorPassableDTO);
            return $next($monitorPassableDTO);
        }

        // Monitor is back online
        if (! $healthy && ! $failedCheck) {
            $this->sendRecovery($monitorPassableDTO);
        }

        return $next($monitorPassableDTO);
    }

    private function sendAlert(MonitorPassableDTO $monitorPassableDTO): void
    {
        Log::debug('Sending alert notification', [
            'monitor_id' => $monitorPassableDTO->monitor->id,
        ]);

        Notification::send(
            $monitorPassableDTO->monitor->user,
            new TriggerAlert(
                monitorName: $monitorPassableDTO->monitor->name,
                monitorId: $monitorPassableDTO->monitor->id,
                reasons: $monitorPassableDTO->getReasons(),
            )
        );

        $monitorPassableDTO->monitor->increment('alert_count');
    }

    private function sendRecovery(MonitorPassableDTO $monitorPassableDTO): void
    {
        Log::debug('Sending recovery notification', [
            'monitor_id' => $monitorPassableDTO->monitor->id,
        ]);

        Notification::send(
            $monitorPassableDTO->monitor->user,
            new MonitorRecovered(
                monitorId: $monitorPassableDTO->monitor->id
            )
        );
    }
}
