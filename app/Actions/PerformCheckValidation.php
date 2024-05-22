<?php

namespace App\Actions;

use App\DTOs\MonitorPassableDTO;
use App\Enums\ComparisonOperator;
use App\Enums\TriggerType;
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
                        triggerName: $trigger->type->getLabel(),
                        reason: $trigger->type->getLabel().' '.$trigger->operator->getLabel().' '.$trigger->value.' triggered.'
                    )
                );
            }
        });

        return $next($monitorPassableDTO);
    }

    protected static function evaluateTrigger(Trigger $trigger, Check $check): bool
    {
        return match ($trigger->type) {
            TriggerType::HTTP_STATUS_CODE => self::compare($check->status_code, $trigger->operator, $trigger->value),
            TriggerType::LATENCY => self::compare($check->response_time, $trigger->operator, $trigger->value),
            default => false,
        };
    }

    protected static function compare($value, $operator, $checkValue): bool
    {
        return match ($operator) {
            ComparisonOperator::EQUALS => $value === $checkValue,
            ComparisonOperator::NOT_EQUALS => $value !== $checkValue,
            ComparisonOperator::GREATER_THAN => $value > $checkValue,
            ComparisonOperator::LESS_THAN => $value < $checkValue,
            ComparisonOperator::GREATER_THAN_OR_EQUALS => $value >= $checkValue,
            ComparisonOperator::LESS_THAN_OR_EQUALS => $value <= $checkValue,
            default => false,
        };
    }
}