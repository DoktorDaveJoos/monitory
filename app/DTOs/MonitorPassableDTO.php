<?php

namespace App\DTOs;

use App\Models\Check;
use App\Models\Monitor;

class MonitorPassableDTO
{
    private bool $failed = false;

    private Check $check;

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

    public function setCheck(Check $check): void
    {
        $this->check = $check;
    }

    public function getCheck(): Check
    {
        return $this->check;
    }
}
