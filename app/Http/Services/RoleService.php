<?php

namespace App\Http\Services;

use App\DTOs\Auth\authenticateDTO;
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

class RoleService
{
    public function __construct() {}
    public function getAllRoles(array $columns = ['*'], array $relations = ['permissions'])
    {
        return Role::select($columns)->with($relations)->paginate(20);
    }
}
