<?php

namespace Database\Factories;

use App\Models\SlackConnection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SlackTokenFactory extends Factory
{
    protected $model = SlackConnection::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'access_token' => $this->faker->sha256,
            'scope' => $this->faker->word,
            'team_id' => $this->faker->uuid,
            'team_name' => $this->faker->company,
            'authed_user_id' => $this->faker->uuid,
        ];
    }
}
