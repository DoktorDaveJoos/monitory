<?php

namespace Tests\Feature;

use App\Actions\PerformCheckAction;
use App\DTOs\MonitorPassableDTO;
use App\Models\Monitor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Before;
use Tests\TestCase;

class PerformCheckActionTest extends TestCase
{
    use RefreshDatabase;

    #[Before]
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_check_is_returned(): void
    {
        Http::fake();

        $monitor = Monitor::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $passable = MonitorPassableDTO::make(
            monitor: $monitor
        );

        $closure = fn ($result) => $result;

        $result = PerformCheckAction::run($passable, $closure);

        $this->assertNotNull($result->getCheck());
    }

    public function test_check_is_returned_with_status_code(): void
    {
        Http::fake([
            'https://example.com' => Http::response(),
        ]);

        $monitor = Monitor::factory()->create([
            'user_id' => $this->user->id,
            'url' => 'https://example.com',
        ]);

        $passable = MonitorPassableDTO::make(
            monitor: $monitor
        );

        $closure = fn ($result) => $result;

        $result = PerformCheckAction::run($passable, $closure);

        $this->assertEquals(200, $result->getCheck()->status_code);
    }

    public function test_check_is_returned_when_request_times_out(): void
    {
        $this->expectException(ConnectionException::class);

        Http::fake(
            fn () => throw new ConnectionException('Request timed out'),
        );

        $monitor = Monitor::factory()->create();

        $passable = MonitorPassableDTO::make(
            monitor: $monitor
        );

        $closure = fn ($result) => $result;

        $result = PerformCheckAction::run($passable, $closure);

        $this->assertEquals('Request timed out', $result->getCheck()->error);
    }
}
