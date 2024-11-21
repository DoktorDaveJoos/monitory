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
        ?string $url,
        ?string $host,
        ?string $method,
        int $interval,
        ?string $auth,
        ?string $auth_username,
        ?string $auth_password,
        ?string $auth_token,
    ): Monitor {
        return $user->monitors()->create([
            'name' => $name,
            'type' => $type,
            'url' => $url,
            'host' => $host,
            'method' => $method,
            'interval' => $interval,
            'auth' => $auth,
            'auth_username' => $auth_username,
            'auth_password' => $auth_password,
            'auth_token' => $auth_token,
        ]);
    }
}
