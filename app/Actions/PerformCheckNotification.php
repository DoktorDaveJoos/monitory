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

            Log::debug('Sending alert', [
                'monitor_id' => $monitorPassableDTO->monitor->id,
                'reasons' => $monitorPassableDTO->getReasons(),
            ]);

            Notification::send(
                $monitorPassableDTO->monitor->user,
                new TriggerAlert(
                    monitorName: $monitorPassableDTO->monitor->name,
                    reasons: $monitorPassableDTO->getReasons(),
                )
            );

            return $next($monitorPassableDTO);
        }

        // Monitor is back online
        if (! $monitorPassableDTO->monitor->success && ! $monitorPassableDTO->failed()) {

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

        return $next($monitorPassableDTO);
    }
}
