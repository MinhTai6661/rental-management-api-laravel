<?php

namespace App\Http\Middleware;

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

        $userPermissions = $user->roles->flatMap(function ($role) {
            return $role->permissions;
        })->pluck('name')->toArray();

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
