<?php

namespace App\Policies;

use App\Constants\RoleConstant;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\Response;

// just learn
class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): Response
    {

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $userToDelete): Response
    {
        $userToDeleteRoles = $userToDelete->roles->pluck('name')->toArray();
        $isSelf = $user->id === $userToDelete->id;
        $canDelete = RoleConstant::canDelete($user->roles->pluck('name')->toArray(), $userToDeleteRoles);
        $condition = !$isSelf && $canDelete;
        
        return $condition ? Response::allow() : Response::deny(__('auth.permission_denied'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): Response
    {
        return Response::deny(__('auth.permission_denied'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): Response
    {
        return Response::deny(__('auth.permission_denied'));
    }
}
