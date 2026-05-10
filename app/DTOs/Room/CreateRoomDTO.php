<?php

namespace App\DTOs\Room;

use App\DTOs\BaseDTO;

class CreateRoomDTO extends BaseDTO
{
    protected const MAP = [
        'name' => 'name',
        'size' => 'size',
        'rental_price' => 'rentalPrice',
        'description' => 'description',
        'dormitory_id' => 'dormitoryId',
        'images' => 'images',
    ];

    public string $name;

    public ?float $size;

    public float $rentalPrice;

    public ?string $description;

    public string $dormitoryId;

    public ?array $images;

    protected array $except = ['images'];
}
