<?php

namespace App\Actions\MonitorStrategies;

use App\Enums\HttpMethod;
use App\Models\Check;
use BadMethodCallException;
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

    public function check(): void
    {
        // Perform the HTTP request
        $start = microtime(true);
        $response = self::httpWithMethod($this->method, $this->url);
        $end = microtime(true);

        // In ms
        $response_time = ($end - $start) * 1000;

        // Save the check
        Check::create([
            'monitor_id' => $this->monitor_id,
            'status_code' => $response->status(),
            'response_time' => (int) $response_time,
            'response_body' => $response->body() ?? null,
            'response_headers' => $response->headers() ? json_encode($response->headers()) : null,
            'started_at' => $start,
            'finished_at' => $end,
        ]);

    }

    /**
     * @throws BadMethodCallException
     */
    private static function httpWithMethod(HttpMethod $method, string $url): Response
    {
        return match ($method) {
            HttpMethod::GET => Http::get($url),
            HttpMethod::POST => Http::post($url),
            HttpMethod::PUT => Http::put($url),
            HttpMethod::DELETE => Http::delete($url),
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
}
