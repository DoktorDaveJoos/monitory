<?php

namespace App\Enums;

enum AuthType: string
{
    case NONE = 'no_auth';
    case BASIC = 'basic_auth';
    case DIGEST = 'digest_auth';
    case BEARER = 'bearer_token';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::NONE => 'No Auth',
            self::BASIC => 'Basic Auth',
            self::DIGEST => 'Digest Auth',
            self::BEARER => 'Bearer Token',
        };
    }
}
