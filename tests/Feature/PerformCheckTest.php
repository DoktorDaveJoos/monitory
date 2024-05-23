<?php

namespace Tests\Feature;

use App\Enums\ActionType;
use App\Enums\HttpMethod;
use App\Enums\Operator;
use App\Enums\TriggerType;
use App\Jobs\PerformCheck;
use App\Models\Monitor;
use App\Models\Trigger;
use App\Notifications\TriggerAlert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PerformCheckTest extends TestCase
{
    use RefreshDatabase;

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
}
