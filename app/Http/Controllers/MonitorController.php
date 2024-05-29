<?php

namespace App\Http\Controllers;

use App\Actions\StoreMonitor;
use App\Actions\UpdateMonitor;
use App\Http\Requests\StoreMonitorRequest;
use App\Http\Requests\UpdateMonitorRequest;
use App\Models\Check;
use App\Models\Monitor;
use App\Rules\CheckDateRange;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
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
            method: $request->validated('method'),
            interval: $request->validated('interval')
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

        return inertia('Monitor/Show', [
            'monitor' => $monitor->load(['triggers']),
            'checks' => Check::whereBelongsTo($monitor)
                ->whereBetween('created_at', [$from, $to])
                ->orderBy('created_at', 'desc')
                ->get(),
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
            type: $request->validated('type'),
            url: $request->validated('url'),
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
