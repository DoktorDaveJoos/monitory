<?php

namespace App\Http\Controllers;

use App\Http\Resources\MonitorResource;
use App\Models\Check;
use App\Models\Monitor;
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

        $hasMonitors = Monitor::all()->count() > 0;
        $totalChecksQuery = Check::whereIn('monitor_id', Monitor::all()->pluck('id'));
        $totalChecksCount = $totalChecksQuery->count();

        return Inertia::render('Dashboard', [
            'monitors' => MonitorResource::collection(
                Monitor::with('checks')
                    ->orderBy('name')
                    ->get()
            ),
            'check_labels' => Check::labels(
                from: now()->subHour(),
                to: now()
            ),
            'stats' => [
                'total_checks' => $totalChecksCount,
                'total_monitors' => Monitor::all()->count(),
                'total_notifications' => Monitor::all()->sum('alert_count'),
                'uptime_overall' => Number::percentage(
                    number: $hasMonitors
                        ? $totalChecksQuery
                            ->where('success', true)
                            ->count() / $totalChecksCount * 100
                        : 0,
                    precision: 2
                ),
                'average_response_time' => Number::format(
                    number: $hasMonitors
                    ? $totalChecksQuery->avg('response_time')
                    : 0,
                    maxPrecision: 0
                ).'ms',
            ],
        ]);
    }
}
