<?php

namespace Database\Seeders;

use App\Enums\ComparisonOperator;
use App\Enums\TriggerType;
use App\Models\Monitor;
use App\Models\Trigger;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
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
                        'comparison_operator' => ComparisonOperator::GREATER_THAN,
                    ])
                    ->create()
            );
    }

    public static function runFor(User $user, int $count = 1): void
    {
        // With one trigger that says: if the HTTP status code is not the expected one, trigger an alert
        Monitor::factory($count)
            ->for($user)
            ->has(
                Trigger::factory()
                    ->state([
                        'type' => TriggerType::HTTP_STATUS_CODE,
                        'value' => Response::HTTP_OK,
                        'comparison_operator' => ComparisonOperator::NOT_EQUALS,
                    ])
                    ->create()
            );
    }
}
