<?php

namespace App\DTOs\User;

use App\DTOs\BaseDTO;

readonly class UpdateProfileDTO extends BaseDTO
{
    protected const MAP = [
        'name'           => 'name',
        'email'          => 'email',
        'phone'          => 'phone',
        'avatar'         => 'avatar',
        'detail_address' => 'detailAddress',
        'ward_code'      => 'wardCode',
    ];

    public ?string $name;
    public ?string $email;
    public ?string $phone;
    public ?string $avatar;
    public ?string $detailAddress;
    public ?int $wardCode;
}