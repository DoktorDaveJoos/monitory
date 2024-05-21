<?php

namespace App\Actions\MonitorStrategies;

use App\Models\Check;

interface MonitorStrategy
{
    public function check(): Check;

    public function validate(): void;

    public static function make(array $attributes): MonitorStrategy;

}
