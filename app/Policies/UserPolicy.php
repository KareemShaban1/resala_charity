<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
        return $user->hasPermissionTo('view users'); // Check permission

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        //
        return $user->hasPermissionTo('view users'); // Check permission

    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return $user->hasPermissionTo('create user'); // Check permission

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        //
        return $user->hasPermissionTo('update user'); // Check permission

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        //
        return $user->hasPermissionTo('delete user'); // Check permission

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        //
        return $user->hasPermissionTo('restore user'); // Check permission

    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        //
        return $user->hasPermissionTo('force delete user'); // Check permission

    }
}
