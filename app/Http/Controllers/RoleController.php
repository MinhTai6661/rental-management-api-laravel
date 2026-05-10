<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UsersUpdateRoleRequest;
use App\Http\Resources\Role\RoleCollection;
use App\Http\Resources\Role\RoleResource;
use App\Http\Services\RoleService;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function list(RoleService $roleService)
    {
        $roles = $roleService->getAllRoles();
        return (new RoleCollection($roles))->additional(['message' => 'Roles retrieved successfully']);
    }
}
