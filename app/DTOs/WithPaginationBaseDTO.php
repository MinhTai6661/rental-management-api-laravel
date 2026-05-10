<?php

namespace App\DTOs;

class WithPaginationBaseDTO extends BaseDTO
{
    protected const MAP = [
        'per_page' => 'perPage',
        'page' => 'page',
        'direction' => 'direction',
        'sort_by' => 'sortBy',
    ];

    public ?int $perPage = 10;

    public ?int $page = 1;

    public ?string $direction;

    public ?string $sortBy;
}
