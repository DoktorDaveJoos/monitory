<?php

use App\Console\Commands\ScheduleChecks;
use App\Enums\Interval;
use Illuminate\Support\Facades\Schedule;

Schedule::command(ScheduleChecks::class, [Interval::MINUTES_5->value])
    ->description('Handling checks with a 5 minute interval')
    ->everyFiveMinutes();

Schedule::command(ScheduleChecks::class, [Interval::MINUTES_1->value])
    ->description('Handling checks with a 1 minute interval')
    ->everyMinute();
