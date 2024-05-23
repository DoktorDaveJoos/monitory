<?php

namespace Tests\Feature;

use App\Enums\ActionType;
use App\Enums\HttpMethod;
use App\Enums\Interval;
use App\Enums\Operator;
use App\Enums\TriggerType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Before;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MonitorTest extends TestCase
{
    use RefreshDatabase;

    const URL = 'https://example.com';

    #[Before]
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function setUserSubscribed(): void
    {
        $this->partialMock(User::class, function ($mock) {
            $mock->shouldReceive('subscribed')->andReturn(true);
        });
    }

    public function test_user_can_view_monitor(): void
    {
        $this->actingAs($this->user);

        $monitor = $this->user->monitors()->create([
            'name' => 'My Monitor',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ]);

        $response = $this->get("/monitors/$monitor->id");

        $response
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Monitor/Show')
                ->has('monitor', fn (AssertableInertia $page) => $page
                    ->where('id', $monitor->id)
                    ->where('name', 'My Monitor')
                    ->where('type', ActionType::HTTP->value)
                    ->where('url', self::URL)
                    ->where('method', HttpMethod::GET->value)
                    ->where('interval', Interval::MINUTES_5->value)
                    ->where('active', true)
                    ->where('triggers', [])
                    ->etc()
                    ->missing('user_id')
                )->has('checks', 0)
            );
    }

    public function test_user_can_view_monitor_with_checks_and_triggers(): void
    {
        $this->actingAs($this->user);

        $monitor = $this->user->monitors()->create([
            'name' => 'My Monitor',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ]);

        $monitor->triggers()->create([
            'value' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'type' => TriggerType::HTTP_STATUS_CODE,
            'operator' => Operator::EQUALS,
        ]);

        $monitor->checks()->create([
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'response_time' => 1000,
            'response_body' => 'Internal Server Error',
            'response_headers' => ['Content-Type' => 'text/html'],
            'started_at' => now()->subMillis(500),
            'finished_at' => now(),
        ]);

        $response = $this->get("/monitors/$monitor->id");

        $response
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Monitor/Show')
                ->has('monitor', fn (AssertableInertia $page) => $page
                    ->where('id', $monitor->id)
                    ->where('name', 'My Monitor')
                    ->where('type', ActionType::HTTP->value)
                    ->where('url', self::URL)
                    ->where('method', HttpMethod::GET->value)
                    ->where('interval', Interval::MINUTES_5->value)
                    ->where('active', true)
                    ->has('triggers', 1, fn (AssertableInertia $page) => $page
                        ->where('value', Response::HTTP_INTERNAL_SERVER_ERROR)
                        ->where('type', TriggerType::HTTP_STATUS_CODE->value)
                        ->where('operator', Operator::EQUALS->value)
                        ->etc()
                    )
                    ->etc()
                    ->missing('user_id')
                )->has('checks', 1, fn (AssertableInertia $page) => $page
                ->where('status_code', Response::HTTP_INTERNAL_SERVER_ERROR)
                ->where('response_time', 1000)
                ->where('response_body', 'Internal Server Error')
                ->where('response_headers', ['Content-Type' => 'text/html'])
                ->etc()
                )
            );
    }

    public function test_user_can_view_monitor_with_non_displayed_recent_checks(): void
    {
        $this->actingAs($this->user);

        $monitor = $this->user->monitors()->create([
            'name' => 'My Monitor',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ]);

        $monitor->checks()->create([
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'response_time' => 1000,
            'response_body' => 'Internal Server Error',
            'response_headers' => ['Content-Type' => 'text/html'],
            'started_at' => now()->subMillis(500),
            'finished_at' => now(),
        ]);

        $from = now()->subHour()->format('Y-m-d H:i:s');
        $to = now()->subMinutes(30)->format('Y-m-d H:i:s');

        $this->get("/monitors/$monitor->id?from=$from&to=$to")
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Monitor/Show')
                ->has('checks', 0)
            );
    }

    public function test_user_can_view_monitor_with_only_recent_checks(): void
    {
        $this->actingAs($this->user);

        $monitor = $this->user->monitors()->create([
            'name' => 'My Monitor',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ]);

        $monitor->checks()->createMany([[
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'response_time' => 1000,
            'response_body' => 'Internal Server Error',
            'response_headers' => ['Content-Type' => 'text/html'],
            'started_at' => now()->subMillis(500),
            'finished_at' => now(),
            'created_at' => now()->subMinute(),
        ], [
            'status_code' => Response::HTTP_OK,
            'response_time' => 500,
            'response_body' => 'OK',
            'response_headers' => ['Content-Type' => 'text/html'],
            'started_at' => now()->subMinutes(10),
            'finished_at' => now()->subMinutes(9),
            'created_at' => now()->subMinutes(9),
        ], [
            'status_code' => Response::HTTP_NOT_FOUND,
            'response_time' => 1500,
            'response_body' => 'Not Found',
            'response_headers' => ['Content-Type' => 'text/html'],
            'started_at' => now()->subHours(2),
            'finished_at' => now()->subHours(2),
            'created_at' => now()->subHours(2),
        ]]);

        $this->assertDatabaseCount('checks', 3);

        $from = now()->subHour()->format('Y-m-d H:i:s');
        $to = now()->subSecond()->format('Y-m-d H:i:s');

        $this->get("/monitors/$monitor->id?from=$from&to=$to")
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Monitor/Show')
                ->has('checks', 2)
            );
    }

    public function test_user_cannot_view_monitor_from_to_more_apart_than_4_hours(): void
    {
        $this->actingAs($this->user);

        $monitor = $this->user->monitors()->create([
            'name' => 'My Monitor',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ]);

        $from = now()->subHours(10)->format('Y-m-d H:i:s');
        $to = now()->subSecond()->format('Y-m-d H:i:s');

        $this->get("/monitors/$monitor->id?from=$from&to=$to")
            ->assertSessionHasErrors(['from', 'to']);
    }

    public function test_user_can_create_monitor(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/monitors', [
            'name' => 'My Monitor',
            'type' => ActionType::HTTP->value,
            'url' => self::URL,
            'method' => HttpMethod::GET->value,
            'interval' => Interval::MINUTES_5->value,
        ]);

        // Inertia redirects to the monitors index page
        $response->assertStatus(302);

        $this->assertDatabaseHas('monitors', [
            'name' => 'My Monitor',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ]);
    }

    public function test_user_can_update_monitor(): void
    {
        $this->actingAs($this->user);

        $monitor = $this->user->monitors()->create([
            'name' => 'My Monitor',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ]);

        $response = $this->put("/monitors/$monitor->id", [
            'name' => 'Updated Monitor',
            'type' => ActionType::HTTP->value,
            'url' => self::URL,
            'method' => HttpMethod::GET->value,
            'interval' => Interval::MINUTES_5->value,
        ]);

        // Inertia redirects to the monitors index page
        $response->assertStatus(302);

        $this->assertDatabaseHas('monitors', [
            'name' => 'Updated Monitor',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ]);
    }

    public function test_user_can_delete_monitor(): void
    {
        $this->actingAs($this->user);

        $monitor = $this->user->monitors()->create([
            'name' => 'My Monitor',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ]);

        $response = $this->delete("/monitors/$monitor->id");

        // Inertia redirects to the monitors index page
        $response->assertStatus(302);

        $this->assertDatabaseMissing('monitors', [
            'name' => 'My Monitor',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ]);
    }

    public function test_user_with_subscription_can_set_interval_to_1_minute()
    {
        $this->setUserSubscribed();

        $this->actingAs($this->user);

        $response = $this->post('/monitors', [
            'name' => 'My Monitor',
            'type' => ActionType::HTTP->value,
            'url' => self::URL,
            'method' => HttpMethod::GET->value,
            'interval' => Interval::MINUTES_1->value,
        ]);

        // Inertia redirects to the monitors index page
        $response->assertStatus(302);

        $this->assertDatabaseHas('monitors', [
            'name' => 'My Monitor',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_1,
        ]);
    }

    public function test_user_without_subscription_cannot_set_interval_to_1_minute()
    {
        $this->actingAs($this->user);

        $response = $this->post('/monitors', [
            'name' => 'My Monitor',
            'type' => ActionType::HTTP->value,
            'url' => self::URL,
            'method' => HttpMethod::GET->value,
            'interval' => Interval::MINUTES_1->value,
        ]);

        $response->assertSessionHasErrors('subscription');
    }

    public function test_user_with_subscription_can_create_more_than_3_monitors()
    {
        $this->setUserSubscribed();

        $this->actingAs($this->user);

        $this->user->monitors()->createMany([[
            'name' => 'Monitor 1',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ], [
            'name' => 'Monitor 2',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ], [
            'name' => 'Monitor 3',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ]]);

        $response = $this->post('/monitors', [
            'name' => 'Monitor 4',
            'type' => ActionType::HTTP->value,
            'url' => self::URL,
            'method' => HttpMethod::GET->value,
            'interval' => Interval::MINUTES_5->value,
        ]);

        // Inertia redirects to the monitors index page
        $response->assertStatus(302);

        $this->assertDatabaseHas('monitors', [
            'name' => 'Monitor 4',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ]);
    }

    public function test_user_without_subscription_cannot_create_more_than_3_monitors()
    {
        $this->actingAs($this->user);

        $this->user->monitors()->createMany([[
            'name' => 'Monitor 1',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ], [
            'name' => 'Monitor 2',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ], [
            'name' => 'Monitor 3',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ]]);

        $response = $this->post('/monitors', [
            'name' => 'Monitor 4',
            'type' => ActionType::HTTP->value,
            'url' => self::URL,
            'method' => HttpMethod::GET->value,
            'interval' => Interval::MINUTES_5->value,
        ]);

        // Check why its not subscribed in IntervalLimit rule

        $response->assertSessionHasErrors('subscription');
    }
}
