<?php

namespace Database\Factories;

use App\Models\Check;
use App\Models\Monitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Check>
 */
class CheckFactory extends Factory
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
            'status_code' => 200,
            'response_time' => 100,
            'response_content' => $this->faker->text,
            'response_headers' => $this->faker->text,
            'started_at' => now(),
            'finished_at' => now(),
            'created_at' => now(),
        ];
    }
}
