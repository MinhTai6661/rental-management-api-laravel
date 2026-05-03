<?php

namespace App\DTOs\User;

use App\DTOs\BaseDTO;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Http\Request;

readonly class createUserDTO extends BaseDTO
{
    protected const MAP = [
        'name'           => 'name',
        'email'          => 'email',
        'password'       => 'password',
        'phone'          => 'phone',
        'role'           => 'role',
        'avatar'         => 'avatar',
        'detail_address' => 'detail_address',
        'status'         => 'status',
        'ward_code'      => 'ward_code',
    ];
    public string $name;
    public string $email;
    public string $password;
    public ?string $phone;
    public string $role;
    public ?string $avatar;
    public ?string $detail_address;
    public ?string $status;
    public ?int $ward_code;
    protected static function defaults(): array
    {
        return [
            'role'   => UserRole::USER->value,
            'status' => UserStatus::INACTIVE->value,
        ];
    }
}
