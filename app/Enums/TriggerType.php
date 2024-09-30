<?php

namespace App\Enums;

enum TriggerType: string
{
    case HTTP_STATUS_CODE = 'http_status_code';
    case LATENCY = 'latency';
    case PING = 'ping';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function casesForType(ActionType $type): array
    {
        return match ($type) {
            ActionType::HTTP => [
                self::HTTP_STATUS_CODE,
                self::LATENCY,
            ],
            ActionType::PING => [
                self::PING,
                self::LATENCY,
            ],
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::HTTP_STATUS_CODE => 'HTTP Status Code',
            self::LATENCY => 'Latency',
            self::PING => 'Ping',
        };
    }

    public function getUnit(): string
    {
        return match ($this) {
            self::HTTP_STATUS_CODE, self::PING => '',
            self::LATENCY => 'ms',
        };
    }
}
