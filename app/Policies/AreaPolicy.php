<?php

namespace App\Policies;

use App\Models\Area;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AreaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
        return $user->hasPermissionTo('view areas');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        //
        return $user->hasPermissionTo('view areas');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
            return $user->hasPermissionTo('create areas');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        //
        return $user->hasPermissionTo('update areas');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        //
        return $user->hasPermissionTo('delete areas');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        //
            return $user->hasPermissionTo('restore areas');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Area $area): bool
    {
        //
        return $user->hasPermissionTo('force delete areas');
    }
}
