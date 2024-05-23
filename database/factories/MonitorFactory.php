<?php

namespace Database\Factories;

use App\Enums\ActionType;
use App\Enums\HttpMethod;
use App\Models\Monitor;
use App\Models\User;
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
            'user_id' => User::factory()->create(),
            'name' => $this->faker->name,
            'type' => ActionType::HTTP,
            'url' => $this->faker->url,
            'method' => HttpMethod::GET,
            'interval' => 5,
            'active' => true,
            'last_checked_at' => now(),
        ];
    }
}
