<?php

namespace App\Actions;

use App\DTOs\MonitorPassableDTO;
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

        if ($monitorPassableDTO->failed()) {
            Notification::send(
                $monitorPassableDTO->monitor->user,
                new TriggerAlert(
                    monitorName: $monitorPassableDTO->monitor->name,
                    reasons: $monitorPassableDTO->getReasons(),
                )
            );
        }

        return $next($monitorPassableDTO);
    }
}
