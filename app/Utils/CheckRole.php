<?php

namespace App\Utils;

use App\Enums\UserRole;
use InvalidArgumentException;

class CheckRole
{

    public static function checkPermission(array $currentUserRoles, array $targetUserRoles, array $permissionMap = []): bool
    {
        self::validate($permissionMap);
        // assign null for users without roles to check permissions
        $targets = empty($targetUserRoles) ? [null] : $targetUserRoles;
        
        foreach ($currentUserRoles as $currentUserRole) {
            foreach ($targets as $targetUserRole) {
                if (!in_array($targetUserRole, $permissionMap[$currentUserRole] ?? [], true)) {
                    return false;
                }
            }
        }
        return true;
    }

    private static function validate(array $permissionMap): void
    {
        if (!config('app.debug')) {
            return;
        }
        foreach ($permissionMap as $role => $allowedTargets) {
            if (UserRole::tryFrom($role) === null) {
                throw new InvalidArgumentException("Key [$role] is not a valid role.");
            }

            if (!is_array($allowedTargets)) {
                throw new InvalidArgumentException("Value of role [$role] must be an array.");
            }

            foreach ($allowedTargets as $target) {
                if ($target !== null && UserRole::tryFrom($target) === null) {
                    throw new InvalidArgumentException("Target role [$target] is not a valid role.");
                }
            }
        }
    }
}
