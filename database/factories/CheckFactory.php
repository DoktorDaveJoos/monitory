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
            'status_code' => $this->faker->randomElement([200, 404, 500]),
            'response_time' => $this->faker->numberBetween(50, 1000),
            'response_body' => null,
            'response_headers' => null,
            'success' => $this->faker->boolean(),
            'started_at' => now(),
            'finished_at' => now(),
            'created_at' => now(),
        ];
    }

    public function withTimeSeries($startTime, $intervalMinutes): CheckFactory|Factory
    {
        return $this->state(function (array $attributes) use ($startTime, $intervalMinutes) {
            static $index = 0;
            $time = (clone $startTime)->addMinutes($index * $intervalMinutes);
            $index++;
            return [
                'created_at' => $time,
                'updated_at' => $time,
                'started_at' => $time,
            ];
        });
    }
}
