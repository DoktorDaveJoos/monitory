<?php

namespace Database\Factories;

use App\Enums\ActionType;
use App\Models\Monitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Monitor>
 */
class MonitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'type' => ActionType::HTTP,
            'url' => $this->faker->url,
            'expected_status_code' => 200,
            'frequency' => 5,
            'timeout' => 60,
            'active' => true,
            'last_checked_at' => now(),
        ];
    }
}
