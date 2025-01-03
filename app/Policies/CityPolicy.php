<?php

namespace App\Policies;

use App\Models\City;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
        return $user->hasPermissionTo('view cities');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        //
        return $user->hasPermissionTo('view cities');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return $user->hasPermissionTo('create cities');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        //
        return $user->hasPermissionTo('update cities');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        //
                return $user->hasPermissionTo('delete cities');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        //
        return $user->hasPermissionTo('restore cities');

    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        //
        return $user->hasPermissionTo('force delete cities');

    }
}
