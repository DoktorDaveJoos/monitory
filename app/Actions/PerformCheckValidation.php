<?php

namespace App\Actions;

use App\DTOs\MonitorPassableDTO;
use Closure;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class PerformCheckValidation
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

        $monitorPassableDTO->monitor->type
            ->strategy($monitorPassableDTO->monitor->toArray())
            ->validate();

        return $next($monitorPassableDTO);
    }
}
