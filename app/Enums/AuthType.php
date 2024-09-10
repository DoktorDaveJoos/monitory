<?php

namespace App\Enums;

use Illuminate\Support\Collection;

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
}
