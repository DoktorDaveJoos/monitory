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

        // Only send alert if monitor is not already in failed state
        if ($monitorPassableDTO->failed() && $monitorPassableDTO->monitor->success) {
            Notification::send(
                $monitorPassableDTO->monitor->user,
                new TriggerAlert(
                    monitorName: $monitorPassableDTO->monitor->name,
                    reasons: $monitorPassableDTO->getReasons(),
                )
            );

            if ($monitorPassableDTO->monitor->success) {
                $monitorPassableDTO->monitor->update(['success' => false]);
            }

            return $next($monitorPassableDTO);
        }

        // Monitor is back online
        if (! $monitorPassableDTO->monitor->success) {
            Notification::send(
                $monitorPassableDTO->monitor->user,
                new MonitorRecovered(
                    monitorId: $monitorPassableDTO->monitor->id
                )
            );

            $monitorPassableDTO->monitor->update(['success' => true]);
        }

        return $next($monitorPassableDTO);
    }
}
