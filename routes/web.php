<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/subscribe', [ProfileController::class, 'subscribe'])->name('subscribe');

    Route::post('/monitors', [MonitorController::class, 'store'])->name('monitor.store');
    Route::get('/monitors/{monitor}', [MonitorController::class, 'show'])->name('monitor.show');
    Route::put('/monitors/{monitor}', [MonitorController::class, 'update'])->name('monitor.update');
    Route::delete('/monitors/{monitor}', [MonitorController::class, 'destroy'])->name('monitor.destroy');
});

require __DIR__.'/auth.php';
