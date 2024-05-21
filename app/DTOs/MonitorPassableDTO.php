<?php

namespace App\DTOs;

use App\Models\Monitor;

class MonitorPassableDTO
{

    private bool $failed = false;

    public function __construct(
        readonly Monitor $monitor,
    ) {
    }

    public static function make(Monitor $monitor): MonitorPassableDTO
    {
        return new self(
            monitor: $monitor,
        );
    }

    public function fail(): void
    {
        $this->failed = true;
    }

    public function hasFailed(): bool
    {
        return $this->failed;
    }



}
