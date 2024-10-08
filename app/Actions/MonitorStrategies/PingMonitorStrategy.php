<?php

namespace App\Actions\MonitorStrategies;

use App\Models\Check;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class PingMonitorStrategy implements MonitorStrategy
{
    use asAction;

    public function __construct(
        public int $monitor_id,
        public string $host
    ) {
    }

    public static function make(array $attributes): MonitorStrategy
    {
        return new self(
            monitor_id: $attributes['id'],
            host: $attributes['host'],
        );
    }

    public function check(): Check
    {
        $start = now();

        // Perform a Ping Request to a server
        $result = Process::run('ping -c 1 '.$this->host);

        // Check if the server is reachable
        $isReachable = ! $result->failed()
            && Str::of($result->output())->contains('1 packets transmitted, 1 packets received, 0.0% packet loss');

        $time = (int) Str::match('/time=([0-9.]+)\sms/', $result->output()) ?? null;

        $end = now();

        Log::debug('Ping output: '.$result->output());

        return Check::create([
            'monitor_id' => $this->monitor_id,
            'status_code' => null,
            'response_time' => (int) $time,
            'response_body' => $result->output(),
            'value' => $isReachable ? 1 : 0,
            'response_headers' => null,
            'started_at' => $start,
            'finished_at' => $end,
        ]);
    }
}
