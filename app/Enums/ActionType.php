<?php

namespace App\Enums;

use App\Actions\MonitorStrategies\HttpMonitorStrategy;
use App\Actions\MonitorStrategies\MonitorStrategy;

enum ActionType: string
{
    case HTTP = 'http';

    public function strategy($attributes): MonitorStrategy
    {
        return match ($this) {
            self::HTTP => HttpMonitorStrategy::make($attributes),
        };
    }
}
