<?php

namespace App\DTOs\User;

use App\DTOs\BaseDTO;
use App\Enums\UserStatus;

class CreateUserDTO extends BaseDTO
{
    protected const MAP = [
        'name' => 'name',
        'email' => 'email',
        'password' => 'password',
        'phone' => 'phone',
        'role' => 'role',
        'avatar' => 'avatar',
        'detail_address' => 'detail_address',
        'status' => 'status',
        'ward_code' => 'ward_code',
        'email_verified_at' => 'email_verified_at',
    ];

    public string $name;

    public string $email;

    public string $password;

    public ?string $phone;

    public string $role;

    public ?string $avatar;

    public ?string $detail_address;

    public ?string $status;

    public ?string $email_verified_at;

    public ?int $ward_code;

}
