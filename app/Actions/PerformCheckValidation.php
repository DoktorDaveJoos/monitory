<?php

namespace App\Actions;

use App\DTOs\MonitorPassableDTO;
use App\Models\Check;
use App\Models\Trigger;
use App\Notifications\TriggerAlert;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
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

        $monitorPassableDTO->monitor->triggers->each(function (Trigger $trigger) use ($monitorPassableDTO) {
            if (self::evaluateTrigger($trigger, $monitorPassableDTO->getCheck())) {

                Log::debug('Trigger matched', [
                    'monitor_id' => $monitorPassableDTO->monitor->id,
                    'trigger_id' => $trigger->id,
                ]);

                // Notify the user
                Notification::send(
                    $monitorPassableDTO->monitor->user,
                    new TriggerAlert(
                        monitorName: $monitorPassableDTO->monitor->name,
                        triggerName: $trigger->type,
                        reason: self::getReason($trigger)
                    )
                );
            }
        });


        return $next($monitorPassableDTO);
    }

    protected static function evaluateTrigger(Trigger $trigger, Check $check): bool
    {
        return match ($trigger->type) {
            'status' => self::compare($check->status_code, $trigger->operator, $trigger->value),
            'response_time' => self::compare($check->response_time, $trigger->operator, $trigger->value),
            default => false,
        };
    }

    protected static function getReason(Trigger $trigger): string
    {
        return match ($trigger->type) {
            'status' => 'Status code '.$trigger->operator.' '.$trigger->value,
            'response_time' => 'Response time '.$trigger->operator.' '.$trigger->value,
            default => '',
        };
    }

    protected static function compare($value, $operator, $checkValue): bool
    {
        return match ($operator) {
            '=' => $value === $checkValue,
            '!=' => $value !== $checkValue,
            '>' => $value > $checkValue,
            '<' => $value < $checkValue,
            '>=' => $value >= $checkValue,
            '<=' => $value <= $checkValue,
            default => false,
        };
    }
}
