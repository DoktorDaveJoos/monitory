<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TriggerController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// For testing purposes only
if (! app()->environment('production')) {
    // Endpoint for testing basic auth
    Route::get('/test/basic-auth', function () {
        // return ok
        return Response::make();
    })->middleware('auth.basic');
}

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/subscribe', [ProfileController::class, 'subscribe'])->name('subscribe');

    Route::post('/monitor', [MonitorController::class, 'store'])->name('monitor.store');
    Route::get('/monitor/{monitor}', [MonitorController::class, 'show'])->name('monitor.show');
    Route::put('/monitor/{monitor}', [MonitorController::class, 'update'])->name('monitor.update');
    Route::delete('/monitor/{monitor}', [MonitorController::class, 'destroy'])->name('monitor.destroy');

    Route::post('/monitor/{monitor}/trigger', [TriggerController::class, 'store'])->name('trigger.store');
    Route::delete('/monitor/{monitor}/trigger/{trigger}', [TriggerController::class, 'destroy'])->name('trigger.destroy');

    Route::get('/trigger/options', [TriggerController::class, 'listOptions'])->name('trigger.options');
});

require __DIR__.'/auth.php';
