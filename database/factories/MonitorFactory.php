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
            'alert_count' => $this->faker->numberBetween(0, 10),
            'success' => fake()->boolean(98),
            'interval' => 5,
            'active' => true,
            'last_checked_at' => now(),
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
