<?php

namespace App\Actions;

use App\DTOs\MonitorPassableDTO;
use App\Models\Check;
use Closure;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class PerformCheckUpdate
{
    use AsAction;

    /**
     * @throws Throwable
     */
    public function handle(MonitorPassableDTO $monitorPassableDTO, Closure $next)
    {
        Log::debug('Performing check update for monitor', [
            'monitor_id' => $monitorPassableDTO->monitor->id,
            'monitor_type' => $monitorPassableDTO->monitor->type->value,
        ]);

        // Check is set to failed by default.
        // Set it to success if no trigger has been matched
        if (! $monitorPassableDTO->failed()) {

            Log::debug('Check passed', [
                'monitor_id' => $monitorPassableDTO->monitor->id,
                'check_id' => $monitorPassableDTO->check->id,
            ]);

            $monitorPassableDTO->check->update(['success' => true]);
            $monitorPassableDTO->monitor->update(['success' => true]);

            return $next($monitorPassableDTO);
        }

        // If not returned yet, check failed
        Log::debug('Check failed', [
            'monitor_id' => $monitorPassableDTO->monitor->id,
            'check_id' => $monitorPassableDTO->check->id,
        ]);

        // Only update monitor. Check defaults to failed
        $monitorPassableDTO->monitor->update(['success' => false]);

        return $next($monitorPassableDTO);
    }
}
