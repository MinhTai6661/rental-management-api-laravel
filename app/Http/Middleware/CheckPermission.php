<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        if (! $user) {
            throw new AuthenticationException(__('unauthenticated'));
        }

        $userPermissions = Permission::query()
            ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->join('user_has_roles', 'role_has_permissions.role_id', '=', 'user_has_roles.role_id')
            ->where('user_has_roles.user_id', $user->id)
            ->distinct()
            ->pluck('permissions.name')
            ->toArray();

        $resource = explode('.', $permission)[0];

        $isAuthorized = in_array($permission, $userPermissions)
            || in_array("$resource.manage", $userPermissions);

        if (! $isAuthorized) {
            return response()->json([
                'status' => 'error',
                'message' => __('forbidden'),
                'required_permission' => $permission,
            ], 403);
        }

        return $next($request);
    }
}
