<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use App\Utils\CheckRole;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    public const CAN_UPDATE = [
        UserRole::ADMIN->value => [UserRole::LANDLORD->value, UserRole::TENANT->value, null],
        UserRole::LANDLORD->value => [],
        UserRole::TENANT->value => [],
    ];
    public function __construct() {}

    public function before(User $user)
    {
        if ($user->hasSuperAdmin()) {
            return Response::allow();
        }
    }

    public function manageRoles(User $user): Response
    {
        return  Response::denyWithStatus(403);;
    }

    public function updateUserRole(User $user, User $targetUser, array $roleIds): Response
    {
        $isSelf = $user->id === $targetUser->id;
        $targetRolesUpdate = Role::whereIn('id', $roleIds)->pluck('name')->toArray();
        $targetUserRoles = $targetUser->roles->pluck('name')->toArray();
        $currentUserRoles = $user->roles->pluck('name')->toArray();
        $hasUpdatePermissionUser = CheckRole::checkPermission(
            $currentUserRoles,
            $targetUserRoles,
            self::CAN_UPDATE
        );

        $canUpdateRoles = CheckRole::checkPermission(
            $currentUserRoles,
            $targetRolesUpdate,
            self::CAN_UPDATE
        );
        // dd($currentUserRoles, $targetRolesUpdate,   $canUpdateRoles, $hasUpdatePermissionUser);
        if (!$hasUpdatePermissionUser || !$canUpdateRoles || $isSelf) {
            return Response::denyWithStatus(403);
        }
        return Response::allow();
    }
}
