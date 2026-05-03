<?php

namespace App\DTOs\User;

use App\DTOs\BaseDTO;

readonly class GetAllUserDTO extends BaseDTO
{
    protected const MAP = [
        'per_page'      => 'perPage',
        'page'          => 'page',
        'status'        => 'status',
        'role'          => 'role',
        'ward_code'     => 'wardCode',
        'province_code' => 'provinceCode',
        'sort_by'       => 'sortBy',
        'direction'     => 'direction',
        'name'          => 'name',
        'email'         => 'email',
    ];
    public int $perPage;
    public int $page;

    // Filters
    public ?string $status;
    public ?string $role;
    public ?int $wardCode;
    public ?int $provinceCode;

    //   Sorting
    public string $sortBy;
    public string $direction;

    //search
    public ?string $name;
    public ?string $email;
}
