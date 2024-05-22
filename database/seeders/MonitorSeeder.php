<?php

namespace Database\Seeders;

use App\Models\Monitor;
use App\Models\User;
use Illuminate\Database\Seeder;

class MonitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Monitor::factory(10)->create();
    }

    public static function runFor(User $user, int $count = 1): void
    {
        Monitor::factory($count)->create([
            'user_id' => $user->id,
        ]);
    }
}
