<?php

namespace App\Enums;

enum BoolOptions: string
{
    case TRUE = 'true';
    case FALSE = 'false';

    public function getLabel(): string
    {
        return match ($this) {
            self::TRUE => 'True',
            self::FALSE => 'False',
        };
    }
}
