<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TriggerController;
use App\Models\SlackConnection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/auth/slack/redirect', function () {
    return Socialite::driver('slack')
        ->asBotUser()
        ->setScopes(['chat:write', 'chat:write.public', 'chat:write.customize'])
        ->redirect();
})->name('oauth.slack.redirect');

Route::get('/auth/slack/callback', function () {
    $bot = Socialite::driver('slack')->asBotUser()->user();

    SlackConnection::updateOrCreate(
        ['user_id' => auth()->id()],
        ['token' => $bot->token]
    );

    return redirect()->route('profile.edit');

})->name('oauth.slack.callback');

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
    Route::patch('/profile/notification-settings', [ProfileController::class, 'updateNotificationSettings'])->name('profile.notification-settings');
    Route::patch('/profile/slack-channel', [ProfileController::class, 'updateSlackChannel'])->name('profile.slack-channel');
    Route::delete('/profile/slack-connection', [ProfileController::class, 'destroySlackConnection'])->name('profile.slack-connection.destroy');
    Route::post('/profile/slack-connection/test', [ProfileController::class, 'testSlackConnection'])->name('profile.slack-connection.test');
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
