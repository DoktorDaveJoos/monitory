<?php

namespace App\Actions;

use App\Models\Monitor;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateMonitor
{
    use AsAction;

    public function handle(
        Monitor $monitor,
        string $name,
        string $type,
        string $url,
        string $method,
        int $interval
    ): Monitor {
        $monitor->update([
            'name' => $name,
            'type' => $type,
            'url' => $url,
            'method' => $method,
            'interval' => $interval,
        ]);

        return $monitor;
    }
}
