<?php

namespace Tests\Feature;

use App\Actions\PerformCheckNotification;
use App\DTOs\MonitorPassableDTO;
use App\Enums\ActionType;
use App\Enums\HttpMethod;
use App\Enums\Operator;
use App\Enums\TriggerType;
use App\Models\Monitor;
use App\Models\Trigger;
use App\Notifications\TriggerAlert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PerformCheckNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_is_sent_when_alert_is_triggered(): void
    {
        Notification::fake();

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

        $passable = MonitorPassableDTO::make(
            monitor: $monitor
        );

        $passable->fail();
        $passable->addReason('HTTP status code not equal to 200');

        PerformCheckNotification::run(
            $passable,
            fn ($result) => $result
        );

        Notification::assertSentTo(
            notifiable: [$monitor->user],
            notification: TriggerAlert::class
        );
    }

    public function test_notification_is_not_sent_when_alert_is_not_triggered(): void
    {
        Notification::fake();

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

        $passable = MonitorPassableDTO::make(
            monitor: $monitor
        );

        PerformCheckNotification::run(
            $passable,
            fn ($result) => $result
        );

        Notification::assertNothingSent();
    }
}
