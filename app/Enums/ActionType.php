<?php

namespace App\Enums;

use App\Actions\MonitorStrategies\HttpMonitorStrategy;
use App\Actions\MonitorStrategies\MonitorStrategy;
use App\Actions\MonitorStrategies\PingMonitorStrategy;

enum ActionType: string
{
    case HTTP = 'http';
    case PING = 'ping';

    public function strategy($attributes): MonitorStrategy
    {
        return match ($this) {
            self::HTTP => HttpMonitorStrategy::make($attributes),
            self::PING => PingMonitorStrategy::make($attributes),
        };
    }
}
