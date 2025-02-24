<?php

namespace App\Policies;

use App\Models\ActivityStatus;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ActivityStatusPolicy
{
   /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
        return $user->hasPermissionTo('view activity statuses');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        //
        return $user->hasPermissionTo('view activity statuses');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
            return $user->hasPermissionTo('create activity status');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        //
        return $user->hasPermissionTo('update activity status');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        //
        return $user->hasPermissionTo('delete activity status');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        //
            return $user->hasPermissionTo('restore activity status');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        //
        return $user->hasPermissionTo('force delete activity status');
    }
}
