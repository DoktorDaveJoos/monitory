<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTriggerRequest;
use App\Http\Requests\UpdateTriggerRequest;
use App\Models\Monitor;
use App\Models\Trigger;
use Illuminate\Support\Facades\Gate;

class TriggerController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTriggerRequest $request, Monitor $monitor)
    {
        Gate::authorize('create', [Trigger::class, $monitor]);

        $monitor->triggers()->create($request->validated());

        return to_route('monitor.show', $monitor);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Monitor $monitor, Trigger $trigger)
    {
        Gate::authorize('delete', $trigger);

        $trigger->delete();

        return to_route('monitor.show', $monitor);
    }
}
