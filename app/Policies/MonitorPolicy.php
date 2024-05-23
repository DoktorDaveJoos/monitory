<?php

namespace App\Policies;

use App\Models\Monitor;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Throwable;

class MonitorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Monitor $monitor): bool
    {
        return $user->id === $monitor->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @throws Throwable
     */
    public function create(User $user): bool
    {
        // Case 1: User has no subscription, then max 3 monitors are allowed
        $maxMonitors = $user->subscribed()
            ? User::MAX_MONITORS_WITH_SUBSCRIPTION
            : User::MAX_MONITORS;

        return $user->monitors()->count() < $maxMonitors;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Monitor $monitor): bool
    {
        // Only the owner of the monitor can update it
        return $user->id === $monitor->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Monitor $monitor): bool
    {
        // Only the owner of the monitor can delete it
        return $user->id === $monitor->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Monitor $monitor): bool
    {
        // Only the subscribed owner of the monitor can restore it
        return $user->id === $monitor->user_id && $user->subscribed();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Monitor $monitor): bool
    {
        return false;
    }
}
