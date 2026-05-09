<?php

namespace App\DTOs\Room;

use App\DTOs\WithPaginationBaseDTO;

class GetRoomsDTO extends WithPaginationBaseDTO
{
    protected const MAP = [
        ...parent::MAP,
        'name' => 'name',
        'size_from' => 'sizeFrom',
        'size_to' => 'sizeTo',
        'rental_price' => 'rentalPrice',
        'status' => 'status',
        'dormitory_id' => 'dormitoryId',
        'ward_code' => 'wardCode',
        'province_code' => 'provinceCode',
    ];

    public ?string $name;

    public ?float $rentalPrice;

    public ?string $status;

    public ?string $dormitoryId;

    public ?int $wardCode;

    public ?int $provinceCode;

    public ?float $sizeFrom;

    public ?float $sizeTo;
}
