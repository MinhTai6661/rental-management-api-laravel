<?php

namespace App\DTOs\Role;

use App\DTOs\BaseDTO;
use App\Enums\UserStatus;

class UpdateRoleDTO extends BaseDTO
{
    protected const MAP = [
        'roles' => 'roles',
    ];

    public array $roles;
}
