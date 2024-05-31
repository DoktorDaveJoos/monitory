<?php

namespace Tests\Feature;

use App\Enums\Operator;
use App\Enums\TriggerType;
use App\Models\Monitor;
use App\Models\User;
use PHPUnit\Framework\Attributes\Before;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TriggerTest extends TestCase
{
    #[Before]
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->has(Monitor::factory())
            ->create();
    }

    public function test_non_authenticated_user_cannot_create_trigger_for_monitor(): void
    {
        $monitor = Monitor::factory()->create();

        $response = $this->post(route('trigger.store', $monitor), [
            'type' => TriggerType::HTTP_STATUS_CODE->value,
            'operator' => Operator::EQUALS->value,
            'value' => Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_create_trigger_for_monitor(): void
    {

        $this->actingAs($this->user);

        $monitor = $this->user->monitors->first();

        $response = $this->post(route('trigger.store', $monitor), [
            'type' => TriggerType::HTTP_STATUS_CODE->value,
            'operator' => Operator::EQUALS->value,
            'value' => Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);

        $response->assertRedirect(route('monitor.show', $monitor));

        $this->assertDatabaseHas('triggers', [
            'monitor_id' => $monitor->id,
            'type' => TriggerType::HTTP_STATUS_CODE,
            'operator' => Operator::EQUALS,
            'value' => Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);
    }

    public function test_user_can_delete_trigger(): void
    {
        $this->actingAs($this->user);

        $monitor = $this->user->monitors->first();

        $trigger = $monitor->triggers()->create([
            'type' => TriggerType::HTTP_STATUS_CODE,
            'operator' => Operator::EQUALS,
            'value' => Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);

        $response = $this->delete(route('trigger.destroy', [
            'monitor' => $monitor,
            'trigger' => $trigger,
        ]));

        $response->assertRedirect(route('monitor.show', $monitor));

        $this->assertDatabaseMissing('triggers', [
            'id' => $trigger->id,
        ]);
    }

    public function test_user_cannot_delete_trigger_for_monitor_they_do_not_own(): void
    {
        $monitor = Monitor::factory()->create();

        $trigger = $monitor->triggers()->create([
            'type' => TriggerType::HTTP_STATUS_CODE,
            'operator' => Operator::EQUALS,
            'value' => Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);

        $this->actingAs($this->user);

        $response = $this->delete(route('trigger.destroy', [
            'monitor' => $monitor,
            'trigger' => $trigger,
        ]));

        $response->assertNotFound();

        $this->assertDatabaseHas('triggers', [
            'id' => $trigger->id,
        ]);
    }

    public function test_user_with_subscription_can_create_more_than_1_trigger(): void
    {

        $this->actingAsSubscribedUser($this->user);

        $monitor = $this->user->monitors->first();

        $monitor->triggers()->create([
            'type' => TriggerType::HTTP_STATUS_CODE,
            'operator' => Operator::EQUALS,
            'value' => Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);

        $response = $this->post(route('trigger.store', $monitor), [
            'type' => TriggerType::HTTP_STATUS_CODE->value,
            'operator' => Operator::EQUALS->value,
            'value' => Response::HTTP_NOT_FOUND,
        ]);

        $response->assertRedirect(route('monitor.show', $monitor));

        $this->assertDatabaseHas('triggers', [
            'monitor_id' => $monitor->id,
            'type' => TriggerType::HTTP_STATUS_CODE,
            'operator' => Operator::EQUALS,
            'value' => Response::HTTP_NOT_FOUND,
        ]);
    }

    public function test_user_without_subscription_cannot_create_more_than_1_trigger(): void
    {
        $this->actingAs($this->user);

        $monitor = $this->user->monitors->first();

        $monitor->triggers()->create([
            'type' => TriggerType::HTTP_STATUS_CODE,
            'operator' => Operator::EQUALS,
            'value' => Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);

        $response = $this->post(route('trigger.store', $monitor), [
            'type' => TriggerType::HTTP_STATUS_CODE->value,
            'operator' => Operator::EQUALS->value,
            'value' => Response::HTTP_NOT_FOUND,
        ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('triggers', [
            'monitor_id' => $monitor->id,
            'type' => TriggerType::HTTP_STATUS_CODE,
            'operator' => Operator::EQUALS,
            'value' => Response::HTTP_NOT_FOUND,
        ]);
    }
}
