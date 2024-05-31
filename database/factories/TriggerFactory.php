<?php

namespace Database\Factories;

use App\Enums\Operator;
use App\Enums\TriggerType;
use App\Models\Monitor;
use App\Models\Trigger;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trigger>
 */
class TriggerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'monitor_id' => Monitor::factory()->create(),
            'type' => TriggerType::HTTP_STATUS_CODE,
            'value' => 500,
            'operator' => Operator::GREATER_THAN,
        ];
    }
}
