<?php

namespace App\Constants;

use App\Enums\UserRole;

class RoleConstant
{

    //null is users without roles
    private const FULL_ROLE = [
        UserRole::ADMIN->value,
        UserRole::LANDLORD->value,
        UserRole::TENANT->value,
        null,
    ];

    public const CAN_DELETE = [
        UserRole::SUPER_ADMIN->value => self::FULL_ROLE,
        UserRole::ADMIN->value       => [UserRole::LANDLORD->value, UserRole::TENANT->value, null],
        UserRole::LANDLORD->value    => [],
        UserRole::TENANT->value      => [],
    ];

    public const CAN_UPDATE = [
        UserRole::SUPER_ADMIN->value => self::FULL_ROLE,
        UserRole::ADMIN->value       => [UserRole::LANDLORD->value, UserRole::TENANT->value, null],
        UserRole::LANDLORD->value    => [],
        UserRole::TENANT->value      => [],
    ];

    public const CAN_CREATE = [
        UserRole::SUPER_ADMIN->value => self::FULL_ROLE,
        UserRole::ADMIN->value       => [UserRole::LANDLORD->value, UserRole::TENANT->value, null],
        UserRole::LANDLORD->value    => [],
        UserRole::TENANT->value      => [],
    ];

    private static function checkPermission(array $currentUserRoles, array $targetUserRoles, array $permissionMap = []): bool
    {

        //assign null for users without roles to check permissions
        $targets = empty($targetUserRoles) ? [null] : $targetUserRoles;

        foreach ($currentUserRoles as $currentUserRole) {
            foreach ($targets as $targetUserRole) {
                if (in_array($targetUserRole, $permissionMap[$currentUserRole] ?? [], true)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function canDelete(array $currentRoles, array $targetRoles): bool
    {
        return self::checkPermission($currentRoles, $targetRoles, self::CAN_DELETE);
    }

    public static function canUpdate(array $currentRoles, array $targetRoles): bool
    {
        return self::checkPermission($currentRoles, $targetRoles, self::CAN_UPDATE);
    }

    public static function canCreate(array $currentRoles, array $targetRoles): bool
    {
        return self::checkPermission($currentRoles, $targetRoles, self::CAN_CREATE);
    }
}
