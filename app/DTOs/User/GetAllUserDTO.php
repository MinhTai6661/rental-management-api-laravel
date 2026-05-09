<?php

namespace App\DTOs\User;

use App\DTOs\WithPaginationBaseDTO;

class GetAllUserDTO extends WithPaginationBaseDTO
{
    protected const MAP = [
        ...parent::MAP,
        'status' => 'status',
        'role' => 'role',
        'ward_code' => 'wardCode',
        'province_code' => 'provinceCode',
        'sort_by' => 'sortBy',
        'name' => 'name',
        'email' => 'email',
    ];

    // Filters
    public ?string $status;

    public ?string $role;

    public ?int $wardCode;

    public ?int $provinceCode;

    //   Sorting
    public ?string $sortBy;

    // search
    public ?string $name;

    public ?string $email;
}
