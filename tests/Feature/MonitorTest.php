<?php

namespace Tests\Feature;

use App\Enums\ActionType;
use App\Enums\AuthType;
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

        $response = $this->get("/monitor/$monitor->id");

        $response
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Monitor/Show')
                ->has('monitor', fn (AssertableInertia $page) => $page
                    ->where('data.id', $monitor->id)
                    ->where('data.name', 'My Monitor')
                    ->where('data.type', ActionType::HTTP->value)
                    ->where('data.url', self::URL)
                    ->where('data.method', HttpMethod::GET->value)
                    ->where('data.interval', Interval::MINUTES_5->value)
                    ->where('data.active', true)
                    ->where('data.checks', [])
                    ->etc()
                    ->missing('user_id')
                )
                ->has('trigger.data', 0)
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

        $response = $this->get("/monitor/$monitor->id");

        $response
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Monitor/Show')
                ->has('monitor', fn (AssertableInertia $page) => $page
                    ->where('data.id', $monitor->id)
                    ->where('data.name', 'My Monitor')
                    ->where('data.type', ActionType::HTTP->value)
                    ->where('data.url', self::URL)
                    ->where('data.method', HttpMethod::GET->value)
                    ->where('data.interval', Interval::MINUTES_5->value)
                    ->where('data.active', true)
                    ->has('data.checks', 1, fn (AssertableInertia $page) => $page
                        ->where('status_code', Response::HTTP_INTERNAL_SERVER_ERROR)
                        ->where('response_time', 1000)
                        ->where('response_body', 'Internal Server Error')
                        ->where('response_headers', ['Content-Type' => 'text/html'])
                        ->etc()
                        ->etc()
                        ->missing('user_id')
                    )
                )
                ->has('trigger.data', 1, fn (AssertableInertia $page) => $page
                    ->where('value', Response::HTTP_INTERNAL_SERVER_ERROR)
                    ->where('type', TriggerType::HTTP_STATUS_CODE->getLabel())
                    ->where('operator', Operator::EQUALS->getLabel())
                    ->etc()
                )
            );
    }
    /**
     * The following test is uncommented.
     *
     * Reason: atm, the monitor will always be delivered with the checks from the last hour.
     * See: MonitorResource::class
     *
     * Reintroduce this test after introduce the feature of scrolling trough the timeline.
     */
    //    public function test_user_can_view_monitor_with_non_displayed_recent_checks(): void
    //    {
    //        $this->actingAs($this->user);
    //
    //        $monitor = $this->user->monitors()->create([
    //            'name' => 'My Monitor',
    //            'type' => ActionType::HTTP,
    //            'url' => self::URL,
    //            'method' => HttpMethod::GET,
    //            'interval' => Interval::MINUTES_5,
    //        ]);
    //
    //        $monitor->checks()->create([
    //            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
    //            'response_time' => 1000,
    //            'response_body' => 'Internal Server Error',
    //            'response_headers' => ['Content-Type' => 'text/html'],
    //            'started_at' => now()->subMillis(500),
    //            'finished_at' => now(),
    //        ]);
    //
    //        $from = now()->subHour()->format('Y-m-d H:i:s');
    //        $to = now()->subMinutes(30)->format('Y-m-d H:i:s');
    //
    //        $this->get("/monitor/$monitor->id?from=$from&to=$to")
    //            ->assertInertia(fn (AssertableInertia $page) => $page
    //                ->component('Monitor/Show')
    //                ->has('monitor.data.checks', 0)
    //            );
    //    }

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

        $this->get("/monitor/$monitor->id?from=$from&to=$to")
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Monitor/Show')
                ->has('monitor.data.checks', 2)
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

        $this->get("/monitor/$monitor->id?from=$from&to=$to")
            ->assertSessionHasErrors(['from', 'to']);
    }

    public function test_user_can_create_monitor(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/monitor', [
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

        $response = $this->put("/monitor/$monitor->id", [
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

        $response = $this->delete("/monitor/$monitor->id");

        // Inertia redirects to the monitors index page
        $response->assertRedirect(route('dashboard'));

        $this->assertSoftDeleted($monitor);
    }

    public function test_user_with_subscription_can_set_interval_to_1_minute()
    {
        $this->actingAsSubscribedUser($this->user);

        $response = $this->post('/monitor', [
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

        $response = $this->post('/monitor', [
            'name' => 'My Monitor',
            'type' => ActionType::HTTP->value,
            'url' => self::URL,
            'method' => HttpMethod::GET->value,
            'interval' => Interval::MINUTES_1->value,
        ]);

        $response->assertSessionHasErrors('interval');
    }

    public function test_user_with_subscription_can_create_more_than_3_monitors()
    {
        $this->actingAsSubscribedUser($this->user);

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

        $response = $this->post('/monitor', [
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

        $response = $this->post('/monitor', [
            'name' => 'Monitor 4',
            'type' => ActionType::HTTP->value,
            'url' => self::URL,
            'method' => HttpMethod::GET->value,
            'interval' => Interval::MINUTES_5->value,
        ]);

        $response->assertForbidden();
    }

    public function test_user_cannot_view_monitor_they_do_not_own(): void
    {
        $monitor = User::factory()->create()->monitors()->create([
            'name' => 'My Monitor',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
        ]);

        $this->actingAs($this->user);

        $response = $this->get("/monitor/$monitor->id");

        $response->assertNotFound();
    }

    public function test_monitor_can_created_with_basic_auth(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/monitor', [
            'name' => 'My Monitor',
            'type' => ActionType::HTTP->value,
            'url' => self::URL,
            'method' => HttpMethod::GET->value,
            'interval' => Interval::MINUTES_5->value,
            'auth' => AuthType::BASIC->value,
            'auth_username' => 'username',
            'auth_password' => 'password',
        ]);

        // Inertia redirects to the monitors index page
        $response->assertStatus(302);

        $this->assertDatabaseHas('monitors', [
            'name' => 'My Monitor',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
            'auth' => 'basic_auth',
            'auth_username' => 'username',
            'auth_password' => 'password',
        ]);
    }

    public function test_monitor_can_created_with_bearer_token_auth(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/monitor', [
            'name' => 'My Monitor',
            'type' => ActionType::HTTP->value,
            'url' => self::URL,
            'method' => HttpMethod::GET->value,
            'interval' => Interval::MINUTES_5->value,
            'auth' => AuthType::BEARER->value,
            'auth_token' => 'token',
        ]);

        // Inertia redirects to the monitors index page
        $response->assertStatus(302);

        $this->assertDatabaseHas('monitors', [
            'name' => 'My Monitor',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
            'auth' => 'bearer_token',
            'auth_token' => 'token',
        ]);
    }

    public function test_monitor_can_created_with_digest_auth(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/monitor', [
            'name' => 'My Monitor',
            'type' => ActionType::HTTP->value,
            'url' => self::URL,
            'method' => HttpMethod::GET->value,
            'interval' => Interval::MINUTES_5->value,
            'auth' => AuthType::DIGEST->value,
            'auth_token' => 'token',
        ]);

        // Inertia redirects to the monitors index page
        $response->assertStatus(302);

        $this->assertDatabaseHas('monitors', [
            'name' => 'My Monitor',
            'type' => ActionType::HTTP,
            'url' => self::URL,
            'method' => HttpMethod::GET,
            'interval' => Interval::MINUTES_5,
            'auth' => 'digest_auth',
            'auth_token' => 'token',
        ]);
    }

    public function test_monitor_with_same_url_can_be_created_by_different_users(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/monitor', [
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

        $this->actingAs(User::factory()->create());

        $response = $this->post('/monitor', [
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
}
