<?php

namespace App\Http\Controllers;

use App\Http\Resources\MonitorResource;
use App\Models\Check;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        $user = $request->user();

        $hasMonitors = $user->monitors()->count() > 0;
        $totalChecksCount = Check::whereIn('monitor_id', $user->monitors()->pluck('id'))->count();

        return Inertia::render('Dashboard', [
            'monitors' => MonitorResource::collection(
                $user->monitors()
                    ->with('checks')
                    ->orderBy('name')
                    ->get()
            ),
            'check_labels' => Check::labels(
                from: now()->subHour(),
                to: now()
            ),
            'stats' => [
                'total_checks' => $totalChecksCount,
                'total_monitors' => $user->monitors()->count(),
                'total_notifications' => $user->monitors()->sum('alert_count'),
                'uptime_overall' => Number::percentage(
                    number: $hasMonitors && $totalChecksCount > 0
                        ? Check::whereIn('monitor_id', $user->monitors()->pluck('id'))
                            ->where('success', true)
                            ->count() / $totalChecksCount * 100
                        : 0,
                    precision: 2
                ),
                'average_response_time' => Number::format(
                    number: $hasMonitors && $totalChecksCount > 0
                    ? Check::whereIn('monitor_id', $user->monitors()->pluck('id'))->avg('response_time')
                    : 0,
                    maxPrecision: 0
                ).'ms',
            ],
        ]);
    }
}
