<?php

namespace Database\Seeders;

use App\Enums\Operator;
use App\Enums\TriggerType;
use App\Models\Check;
use App\Models\Monitor;
use App\Models\Trigger;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class MonitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Monitor::factory(10)
            ->for(User::factory())
            ->has(
                Trigger::factory()
                    ->state([
                        'type' => TriggerType::HTTP_STATUS_CODE,
                        'value' => 500,
                        'operator' => Operator::GREATER_THAN,
                    ])
            )->create();
    }

    public static function runFor(User $user, int $count = 1): void
    {
        // Time series for the checks
        $startTime = Carbon::now()->subMinutes(60);

        // With one trigger that says: if the HTTP status code is not the expected one, trigger an alert
        Monitor::factory($count)
            ->for($user)
            ->has(
                Trigger::factory()
                    ->state([
                        'type' => TriggerType::HTTP_STATUS_CODE,
                        'value' => Response::HTTP_OK,
                        'operator' => Operator::NOT_EQUALS,
                    ])
            )
            ->has(
                Check::factory()
                    ->count(60)
                    ->state(new Sequence(
                        ...array_map(
                            fn ($i) => [
                                'created_at' => $startTime->copy()->addMinutes($i),
                                'updated_at' => $startTime->copy()->addMinutes($i),
                                'started_at' => $startTime->copy()->addMinutes($i),
                            ],
                            range(0, 59)
                        )
                    ))
            )
            ->create();
    }
}
