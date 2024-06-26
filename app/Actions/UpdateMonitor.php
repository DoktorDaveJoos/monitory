<?php

namespace App\Actions;

use App\Models\Monitor;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateMonitor
{
    use AsAction;

    public function handle(
        Monitor $monitor,
        ?string $method,
        ?int $interval
    ): Monitor {

        $data = [];

        if ($method) {
            $data['method'] = $method;
        }

        if ($interval) {
            $data['interval'] = $interval;
        }

        if (empty($data)) {
            return $monitor;
        }

        $monitor->update($data);

        return $monitor;
    }
}
