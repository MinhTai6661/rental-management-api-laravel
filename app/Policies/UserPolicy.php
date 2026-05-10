<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use App\Utils\CheckRole;
use Illuminate\Auth\Access\Response;

// just learn
class UserPolicy
{

    public const CAN_DELETE = [
        UserRole::ADMIN->value => [UserRole::LANDLORD->value, UserRole::TENANT->value, null],
        UserRole::LANDLORD->value => [],
        UserRole::TENANT->value => [],
    ];

    public const CAN_UPDATE = [
        UserRole::ADMIN->value => [UserRole::LANDLORD->value, UserRole::TENANT->value, null],
        UserRole::LANDLORD->value => [],
        UserRole::TENANT->value => [],
    ];

    public const CAN_CREATE = [
        UserRole::ADMIN->value => [UserRole::LANDLORD->value, UserRole::TENANT->value, null],
        UserRole::LANDLORD->value => [],
        UserRole::TENANT->value => [],
    ];

    public function before(User $user, string $ability): ?Response
    {
        if ($user->hasSuperAdmin()) {
            return Response::allow();
        }
        return null; 
    }
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
        $canDelete = CheckRole::checkPermission(
            $user->roles->pluck('name')->toArray(),
            $userToDeleteRoles,
            self::CAN_DELETE
        );
        $condition = ! $isSelf && $canDelete;

        return $condition ? Response::allow() : Response::denyWithStatus(403);;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): Response
    {
        return Response::denyWithStatus(403);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): Response
    {
        return Response::denyWithStatus(403);
    }
}
