<?php

namespace App\Http\Controllers;

use App\DTOs\Role\UpdateRoleDTO;
use App\Http\Requests\User\UsersUpdateRoleRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Role\RoleCollection;
use App\Http\Resources\Role\RoleResource;
use App\Http\Services\RoleService;
use App\Http\Services\UserRoleService;
use App\Models\Role;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function updateUserRoles(UsersUpdateRoleRequest $request, User $user, UserRoleService $userRoleService)
    {
        $roleIds = $request->validated()['roles'] ?? [];
        // dd($roleIds);
        Gate::authorize('updateUserRole', [Role::class, $user, $roleIds]);
        $dto = UpdateRoleDTO::fromRequest($request);
        $userRoleService->updateUserRoles($user, $dto);

        return (new BaseResource(null))->additional([
            'message' => 'User roles updated successfully',
        ]);
    }
}
