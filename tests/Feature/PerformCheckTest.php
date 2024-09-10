<?php

namespace Tests\Feature;

use App\Enums\ActionType;
use App\Enums\HttpMethod;
use App\Enums\Operator;
use App\Enums\TriggerType;
use App\Jobs\PerformCheck;
use App\Models\Monitor;
use App\Models\Trigger;
use App\Models\User;
use App\Notifications\MonitorRecovered;
use App\Notifications\TriggerAlert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Before;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PerformCheckTest extends TestCase
{
    use RefreshDatabase;

    #[Before]
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_check_has_performed_and_triggered_an_alert(): void
    {
        Notification::fake();

        Http::fake(
            fn ($request) => Http::response(['status' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR)
        );

        $monitor = Monitor::factory()
            ->has(
                Trigger::factory()
                    ->state([
                        'type' => TriggerType::HTTP_STATUS_CODE,
                        'value' => Response::HTTP_OK,
                        'operator' => Operator::NOT_EQUALS,
                    ])
            )->create([
                'user_id' => $this->user->id,
                'type' => ActionType::HTTP,
                'method' => HttpMethod::GET,
            ]);

        (new PerformCheck($monitor->id))->handle();

        $this->assertDatabaseHas('checks', [
            'monitor_id' => $monitor->id,
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);

        Http::assertSentCount(1);

        Notification::assertSentTo(
            notifiable: [$monitor->user],
            notification: TriggerAlert::class
        );
    }

    public function test_check_has_performed_without_triggering_an_alert(): void
    {
        Notification::fake();

        Http::fake(
            fn ($request) => Http::response(['status' => 'ok'], Response::HTTP_OK)
        );

        $monitor = Monitor::factory()
            ->has(
                Trigger::factory()
                    ->state([
                        'type' => TriggerType::HTTP_STATUS_CODE,
                        'value' => Response::HTTP_OK,
                        'operator' => Operator::NOT_EQUALS,
                    ])
            )->create([
                'user_id' => $this->user->id,
                'type' => ActionType::HTTP,
                'method' => HttpMethod::GET,
            ]);

        (new PerformCheck($monitor->id))->handle();

        $this->assertDatabaseHas('checks', [
            'monitor_id' => $monitor->id,
            'status_code' => Response::HTTP_OK,
        ]);

        Http::assertSentCount(1);

        Notification::assertNothingSent();
    }

    public function test_check_sends_recovery_notification(): void
    {
        Notification::fake();

        Http::fake(
            fn ($request) => Http::response(['status' => 'ok'], Response::HTTP_OK)
        );

        $monitor = Monitor::factory()
            ->has(
                Trigger::factory()
                    ->state([
                        'type' => TriggerType::HTTP_STATUS_CODE,
                        'value' => Response::HTTP_OK,
                        'operator' => Operator::NOT_EQUALS,
                    ])
            )->create([
                'user_id' => $this->user->id,
                'type' => ActionType::HTTP,
                'method' => HttpMethod::GET,
                'success' => false, // Simulate a previous failure
            ]);

        // First check
        (new PerformCheck($monitor->id))->handle();

        // Second check
        Http::fake(
            fn ($request) => Http::response(['status' => 'ok'], Response::HTTP_OK)
        );

        (new PerformCheck($monitor->id))->handle();

        $this->assertDatabaseHas('checks', [
            'monitor_id' => $monitor->id,
            'status_code' => Response::HTTP_OK,
        ]);

        Http::assertSentCount(1);

        Notification::assertSentTo(
            notifiable: [$monitor->user],
            notification: MonitorRecovered::class
        );
    }
}
