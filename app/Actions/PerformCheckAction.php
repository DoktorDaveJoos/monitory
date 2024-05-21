<?php

namespace App\Actions;

use App\DTOs\MonitorPassableDTO;
use Closure;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class PerformCheckAction
{
    use AsAction;

    /**
     * @throws Throwable
     */
    public function handle(MonitorPassableDTO $monitorPassableDTO, Closure $next)
    {
        Log::info('Performing check for monitor', [
            'monitor_id' => $monitorPassableDTO->monitor->id,
            'monitor_type' => $monitorPassableDTO->monitor->type->value,
        ]);

        $monitorPassableDTO->monitor->type
            ->strategy($monitorPassableDTO->monitor->toArray())
            ->check();

        return $next($monitorPassableDTO);
    }
}
