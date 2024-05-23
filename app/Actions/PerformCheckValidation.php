<?php

namespace App\Actions;

use App\DTOs\MonitorPassableDTO;
use App\Enums\Operator;
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

        $monitorPassableDTO->monitor->triggers->each(function (Trigger $trigger) use (&$monitorPassableDTO) {
            if (self::evaluateTrigger($trigger, $monitorPassableDTO->getCheck())) {

                Log::debug('Trigger matched', [
                    'monitor_id' => $monitorPassableDTO->monitor->id,
                    'trigger_id' => $trigger->id,
                ]);

                $monitorPassableDTO->fail();

                $monitorPassableDTO->addReason(
                    sprintf(
                        '%s %s %s triggered.',
                        $trigger->type->getLabel(),
                        $trigger->operator->getLabel(),
                        $trigger->value
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

    protected static function compare($checkValue, $operator, $value): bool
    {
        return match ($operator) {
            Operator::EQUALS => $checkValue === $value,
            Operator::NOT_EQUALS => $checkValue !== $value,
            Operator::GREATER_THAN => $checkValue > $value,
            Operator::LESS_THAN => $checkValue < $value,
            Operator::GREATER_THAN_OR_EQUALS => $checkValue >= $value,
            Operator::LESS_THAN_OR_EQUALS => $checkValue <= $value,
            default => false,
        };
    }
}
