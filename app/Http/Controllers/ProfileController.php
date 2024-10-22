<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use App\Notifications\TestSlackConnectionNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => UserResource::make($request->user()),
        ]);
    }

    public function updateNotificationSettings(Request $request): RedirectResponse
    {

        $request->validate([
            'settings' => 'array',
            'settings.notifications.email' => 'boolean',
            'settings.notifications.slack' => 'boolean',
        ]);

        $request->user()->update([
            'settings' => $request->input('settings'),
        ]);

        return Redirect::route('profile.edit');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    public function updateSlackChannel(Request $request): RedirectResponse
    {
        $request->validate([
            'channel' => ['required', 'string', 'max:255'],
        ]);

        $request->user()->slackConnection()->update([
            'channel' => $request->input('channel'),
        ]);

        return Redirect::route('profile.edit');
    }

    public function testSlackConnection(Request $request): RedirectResponse
    {
        $request->user()->notify(new TestSlackConnectionNotification);

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function destroySlackConnection(Request $request): RedirectResponse
    {
        $request->user()->slackConnection()->delete();

        return Redirect::route('profile.edit');
    }
}
