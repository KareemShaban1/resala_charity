<?php

namespace App\Policies;

use App\Models\ActivityReason;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ActivityReasonPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
        return $user->hasPermissionTo('view activity reasons');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        //
        return $user->hasPermissionTo('view activity reasons');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
            return $user->hasPermissionTo('create activity reason');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        //
        return $user->hasPermissionTo('update activity reason');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        //
        return $user->hasPermissionTo('delete activity reason');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        //
            return $user->hasPermissionTo('restore activity reason');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        //
        return $user->hasPermissionTo('force delete activity reason');
    }
}
