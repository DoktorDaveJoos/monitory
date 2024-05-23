<?php

namespace App\Actions;

use App\Models\Monitor;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreMonitor
{
    use AsAction;

    public function handle(
        User $user,
        string $name,
        string $type,
        string $url,
        string $method,
        int $interval
    ): Monitor {
        return $user->monitors()->create([
            'name' => $name,
            'type' => $type,
            'url' => $url,
            'method' => $method,
            'interval' => $interval,
        ]);
    }
}
