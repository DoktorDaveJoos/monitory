<?php

namespace App\Actions\MonitorStrategies;

interface MonitorStrategy
{

    public function check(): void;

    public static function make(array $attributes): MonitorStrategy;

}
