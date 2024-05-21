<?php

namespace App\Actions\MonitorStrategies;

use App\Enums\HttpMethod;
use App\Models\Check;
use App\Models\Monitor;
use BadMethodCallException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class HttpMonitorStrategy implements MonitorStrategy
{
    use asAction;

    public function __construct(
        public int $monitor_id,
        public string $url,
        public HttpMethod $method,
        public string $expectedStatusCode,
    ) {
    }

    /**
     * @throws ConnectionException
     */
    public function check(): Check
    {
        $start = microtime(true);

        $response = self::performHttpRequest(
            method: $this->method,
            timeout: 60,
            connectTimout: 30,
            url: $this->url
        );

        $end = microtime(true);

        // In ms
        $response_time = ($end - $start) * 1000;

        $check = Check::create([
            'monitor_id' => $this->monitor_id,
            'status_code' => $response->status(),
            'response_time' => (int) $response_time,
            'response_body' => $response->body() ?? null,
            'response_headers' => $response->headers() ? json_encode($response->headers()) : null,
            'started_at' => $start,
            'finished_at' => $end,
        ]);

        Monitor::find($this->monitor_id)->update([
            'last_check' => now(),
        ]);

        return $check;
    }

    /**
     * @throws BadMethodCallException
     * @throws ConnectionException
     */
    private static function performHttpRequest(
        HttpMethod $method,
        int $timeout,
        int $connectTimout,
        string $url
    ): Response {
        $client = Http::timeout($timeout)
            ->connectTimeout($connectTimout);

        return match ($method) {
            HttpMethod::GET => $client->get($url),
            HttpMethod::POST => $client->post($url),
            HttpMethod::PUT => $client->put($url),
            HttpMethod::DELETE => $client->delete($url),
        };
    }

    public static function make(array $attributes): MonitorStrategy
    {
        return new self(
            monitor_id: $attributes['id'],
            url: $attributes['url'],
            method: HttpMethod::tryFrom($attributes['method']),
            expectedStatusCode: $attributes['expected_status_code'],
        );
    }

    public function validate(): void
    {

        $monitor = Monitor::find($this->monitor_id);



    }
}
