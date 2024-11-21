<?php

namespace App\Actions\MonitorStrategies;

use App\Enums\AuthType;
use App\Enums\HttpMethod;
use App\Models\Check;
use App\Models\Monitor;
use BadMethodCallException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
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
    ) {
    }

    public static function make(array $attributes): MonitorStrategy
    {
        return new self(
            monitor_id: $attributes['id'],
            url: $attributes['url'],
            method: HttpMethod::tryFrom($attributes['method']),
        );
    }

    /**
     * @throws ConnectionException
     */
    public function check(): Check
    {
        $monitor = Monitor::find($this->monitor_id);

        $start = microtime(true);

        $response = self::performHttpRequest(
            method: $this->method,
            timeout: 60,
            connectTimout: 30,
            url: $this->url,
            auth: $monitor->auth,
            auth_username: $monitor->auth_username,
            auth_password: $monitor->auth_password,
            auth_token: $monitor->auth_token,
        );

        $end = microtime(true);

        // In ms
        $response_time = ($end - $start) * 1000;

        return Check::create([
            'monitor_id' => $this->monitor_id,
            'status_code' => $response->status(),
            'response_time' => (int) $response_time,
            'response_body' => null,
            'response_headers' => $response->headers() ? json_encode($response->headers()) : null,
            'started_at' => $start,
            'finished_at' => $end,
        ]);
    }

    /**
     * @throws BadMethodCallException|ConnectionException
     */
    private static function performHttpRequest(
        HttpMethod $method,
        int $timeout,
        int $connectTimout,
        string $url,
        ?AuthType $auth = null,
        ?string $auth_username = null,
        ?string $auth_password = null,
        ?string $auth_token = null,
    ): Response {
        $client = self::buildClient(
            timeout: $timeout,
            connectTimeout: $connectTimout,
            auth: $auth,
            auth_username: $auth_username,
            auth_password: $auth_password,
            auth_token: $auth_token,
        );

        return match ($method) {
            HttpMethod::GET => $client->get($url),
            HttpMethod::POST => $client->post($url),
            HttpMethod::PUT => $client->put($url),
            HttpMethod::DELETE => $client->delete($url),
        };
    }

    private static function buildClient(
        int $timeout,
        int $connectTimeout,
        ?AuthType $auth,
        ?string $auth_username,
        ?string $auth_password,
        ?string $auth_token,
    ): PendingRequest {
        $client = Http::timeout($timeout)
            ->connectTimeout($connectTimeout);

        return match ($auth) {
            AuthType::BASIC => $client->withBasicAuth($auth_username, $auth_password),
            AuthType::DIGEST => $client->withDigestAuth($auth_username, $auth_password),
            AuthType::BEARER => $client->withToken($auth_token),
            default => $client,
        };
    }
}
