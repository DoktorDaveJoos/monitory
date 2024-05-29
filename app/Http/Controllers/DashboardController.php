<?php

namespace App\Http\Controllers;

use App\Http\Resources\MonitorResource;
use App\Models\Check;
use App\Models\Monitor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
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
        ]);
    }
}
