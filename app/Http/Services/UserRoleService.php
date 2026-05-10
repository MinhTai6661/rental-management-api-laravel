<?php

namespace App\Http\Services;

use App\DTOs\Auth\authenticateDTO;
use App\DTOs\Role\UpdateRoleDTO;
use App\DTOs\User\CreateUserDTO;
use App\Enums\ProviderEnum;
use App\Models\PasswordResetTokens;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserRoleService
{
    public function __construct(
        private RoleService $roleService,
        private UserService $userService
    ) {}
    public function updateUserRoles(User $user, UpdateRoleDTO $rolesDto)
    {
        $cleanRoles = collect($rolesDto->roles)
        ->filter(fn($value) => !is_null($value) && $value !== '')
        ->toArray();
        // dd($cleanRoles);
        return $user->roles()->sync($cleanRoles);
    }
}
