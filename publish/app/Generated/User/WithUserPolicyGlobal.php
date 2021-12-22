<?php

namespace App\Generated\User;

use App\Models\User;

trait WithUserPolicyGlobal
{
    /**
     * Determine whether the user can take action on the model
     *
     * @param \App\Models\User $userWithRequest
     * @return bool
     */
    public function before(User $userWithRequest)
    {
        if($userWithRequest->hasRole("Super Admin")){
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $userWithRequest
     * @return bool
     */
    public function viewAny(User $userWithRequest): bool
    {
        return $userWithRequest->hasPermissionTo("user.view.*");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $userWithRequest
     * @param \App\Models\User $user
     * @return bool
     */
    public function view(User $userWithRequest, User $user): bool
    {
        return $userWithRequest->hasPermissionTo("user.view.{$user->id}");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $userWithRequest
     * @return bool
     */
    public function create(User $userWithRequest): bool
    {
        return $userWithRequest->hasPermissionTo("user.create");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $userWithRequest
     * @param \App\Models\User $user
     * @return bool
     */
    public function update(User $userWithRequest, User $user): bool
    {
        return $userWithRequest->hasPermissionTo("user.update.{$user->id}");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $userWithRequest
     * @param \App\Models\User $user
     * @return bool
     */
    public function delete(User $userWithRequest, User $user): bool
    {
        return $userWithRequest->hasPermissionTo("user.delete.{$user->id}");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $userWithRequest
     * @param \App\Models\User $user
     * @return bool
     */
    public function restore(User $userWithRequest, User $user): bool
    {
        return $userWithRequest->hasPermissionTo("user.restore.{$user->id}");
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $userWithRequest
     * @param \App\Models\User $user
     * @return bool
     */
    public function forceDelete(User $userWithRequest, User $user): bool
    {
        return $userWithRequest->hasPermissionTo("user.delete.{$user->id}");
    }
}



