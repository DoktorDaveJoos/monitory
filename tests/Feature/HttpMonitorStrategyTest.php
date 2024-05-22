<?php

namespace Tests\Feature;

use App\Actions\MonitorStrategies\HttpMonitorStrategy;
use App\Enums\ActionType;
use App\Enums\HttpMethod;
use App\Models\Monitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class HttpMonitorStrategyTest extends TestCase
{
    use RefreshDatabase;

    const URL = 'https://example.com';

    #[DataProvider('methodProvider')]
    public function test_check_created_with_method(HttpMethod $method): void
    {
        Http::fake([
            self::URL => Http::response(),
        ]);

        $monitor = Monitor::factory()->create([
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => $method,
        ]);

        $strategy = HttpMonitorStrategy::make(
            attributes: $monitor->toArray()
        );

        $strategy->check();

        $this->assertDatabaseHas('checks', [
            'monitor_id' => $monitor->id,
            'status_code' => 200,
        ]);

        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request) => $request->method() === $method->value);
    }

    #[DataProvider('expectedStatusCodeProvider')]
    public function test_check_created_with_status_code(int $status): void
    {
        Http::fake([
            self::URL => Http::response(status: $status),
        ]);

        $monitor = Monitor::factory()->create([
            'type' => ActionType::HTTP,
            'url' => self::URL,
        ]);

        $strategy = HttpMonitorStrategy::make(
            attributes: $monitor->toArray()
        );

        $strategy->check();

        $this->assertDatabaseHas('checks', [
            'monitor_id' => $monitor->id,
            'status_code' => $status,
        ]);
    }

    public function test_check_created_if_request_times_out(): void
    {
        $this->expectException(ConnectionException::class);

        Http::fake(
            fn () => throw new ConnectionException('Connection timed out')
        );

        $monitor = Monitor::factory()->create([
            'type' => ActionType::HTTP,
        ]);

        $strategy = HttpMonitorStrategy::make(
            attributes: $monitor->toArray()
        );

        $strategy->check();

        $this->assertDatabaseHas('checks', [
            'monitor_id' => $monitor->id,
            'status_code' => Response::HTTP_SERVICE_UNAVAILABLE,
        ]);
    }

    public function test_check_is_returned(): void
    {
        Http::fake([
            self::URL => Http::response(),
        ]);

        $monitor = Monitor::factory()->create([
            'type' => ActionType::HTTP,
            'url' => self::URL,
        ]);

        $strategy = HttpMonitorStrategy::make(
            attributes: $monitor->toArray()
        );

        $check = $strategy->check();

        $this->assertNotNull($check);
        $this->assertThat($check->monitor_id, $this->equalTo($monitor->id));
    }

    public static function methodProvider(): array
    {
        return [
            'get' => [HttpMethod::GET],
            'post' => [HttpMethod::POST],
            'put' => [HttpMethod::PUT],
            'delete' => [HttpMethod::DELETE],
        ];
    }

    public static function expectedStatusCodeProvider(): array
    {
        return [
            'status code 200' => [200],
            'status code 404' => [404],
            'status code 500' => [500],
        ];
    }
}
