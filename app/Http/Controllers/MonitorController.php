<?php

namespace App\Http\Controllers;

use App\Actions\StoreMonitor;
use App\Actions\UpdateMonitor;
use App\Enums\ActionType;
use App\Enums\HttpStatusCode;
use App\Enums\Operator;
use App\Enums\TriggerType;
use App\Http\Requests\StoreMonitorRequest;
use App\Http\Requests\UpdateMonitorRequest;
use App\Http\Resources\EnumOptionResource;
use App\Http\Resources\MonitorResource;
use App\Http\Resources\OperatorsResource;
use App\Http\Resources\TriggerResource;
use App\Models\Check;
use App\Models\Monitor;
use App\Rules\CheckDateRange;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Number;
use Inertia\Response;

class MonitorController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMonitorRequest $request): RedirectResponse
    {
        Gate::authorize('create', Monitor::class);

        $monitor = StoreMonitor::run(
            user: $request->user(),
            name: $request->validated('name'),
            type: $request->validated('type'),
            url: $request->validated('url'),
            host: $request->validated('host'),
            method: $request->validated('method'),
            interval: $request->validated('interval'),
            auth: $request->validated('auth'),
            auth_username: $request->validated('auth_username'),
            auth_password: $request->validated('auth_password'),
            auth_token: $request->validated('auth_token'),
        );

        return to_route('monitor.show', $monitor->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Monitor $monitor, Request $request): Response
    {
        Gate::authorize('view', $monitor);

        $request->validate([
            'from' => ['nullable', 'date', new CheckDateRange],
            'to' => ['nullable', 'date', new CheckDateRange],
        ]);

        // Default to the last hour
        if (! $request->has('from') && ! $request->has('to')) {
            $to = Carbon::now();
            $from = $to->copy()->subHour();
        } else {
            $from = Carbon::parse($request->input('from'));
            $to = Carbon::parse($request->input('to'));
        }

        // Statistics
        $absolute = Check::whereBelongsTo($monitor)->count();

        $successAbsolute = Check::whereBelongsTo($monitor)->whereBetween('status_code', [200, 299])->count();
        $successTrend = $successAbsolute ? $successAbsolute / $absolute * 100 : 0;

        $fourHundredsAbsolute = Check::whereBelongsTo($monitor)->whereBetween('status_code', [400, 499])->count();
        $fourHundredsTrend = $fourHundredsAbsolute ? $fourHundredsAbsolute / $absolute * 100 : 0;

        $fiveHundredsAbsolute = Check::whereBelongsTo($monitor)->whereBetween('status_code', [500, 599])->count();
        $fiveHundredsTrend = $fiveHundredsAbsolute ? $fiveHundredsAbsolute / $absolute * 100 : 0;

        $timeoutsAbsolute = Check::whereBelongsTo($monitor)->where('status_code', HttpStatusCode::SERVICE_UNAVAILABLE)->count();
        $timeoutsTrend = $timeoutsAbsolute ? $timeoutsAbsolute / $absolute * 100 : 0;

        $averageLatencyOverall = Check::whereBelongsTo($monitor)->avg('response_time') ?? 0;
        $averageLastHour = Check::whereBelongsTo($monitor)->whereBetween('created_at', [Carbon::now()->subHour(), Carbon::now()])->avg('response_time') * 100;
        $latencyTrendsLastHour = $averageLatencyOverall && $averageLastHour > 0
            ? $averageLatencyOverall / $averageLastHour
            : 0;

        $triggers = TriggerType::casesForType($monitor->type);

        return inertia('Monitor/Show', [
            'monitor' => MonitorResource::make($monitor),
            'check_labels' => Check::labels($from, $to),
            'trigger' => TriggerResource::collection($monitor->triggers),
            'trigger_options' => [
                'trigger_types' => EnumOptionResource::collection($triggers),
                'operators' => OperatorsResource::collection($triggers),
                'http_status_codes' => $monitor->type === ActionType::HTTP
                        ? EnumOptionResource::collection(HttpStatusCode::cases())
                        : [],
            ],
            'monitor_stats' => [
                '2xx' => [
                    'absolute' => Number::abbreviate($successAbsolute),
                    'percentage' => Number::percentage($successTrend),
                ],
                '4xx' => [
                    'absolute' => Number::abbreviate($fourHundredsAbsolute),
                    'percentage' => Number::percentage($fourHundredsTrend),
                ],
                '5xx' => [
                    'absolute' => Number::abbreviate($fiveHundredsAbsolute),
                    'percentage' => Number::percentage($fiveHundredsTrend),
                ],
                'timeouts' => [
                    'absolute' => Number::abbreviate($timeoutsAbsolute),
                    'percentage' => Number::percentage($timeoutsTrend),
                ],
                'latency' => [
                    'overall' => Number::abbreviate($averageLatencyOverall),
                    'last_hour' => Number::percentage($latencyTrendsLastHour),
                ],
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMonitorRequest $request, Monitor $monitor)
    {
        Gate::authorize('update', $monitor);

        $monitor = UpdateMonitor::run(
            monitor: $monitor,
            name: $request->validated('name'),
            method: $request->validated('method'),
            interval: $request->validated('interval')
        );

        return to_route('monitor.show', $monitor->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Monitor $monitor)
    {
        Gate::authorize('delete', $monitor);

        $monitor->delete();

        return to_route('dashboard');
    }
}
