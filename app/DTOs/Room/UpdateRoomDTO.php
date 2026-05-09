<?php

namespace App\DTOs\Room;

use App\DTOs\BaseDTO;

class UpdateRoomDTO extends BaseDTO
{
    protected const MAP = [
        'name' => 'name',
        'size' => 'size',
        'rental_price' => 'rentalPrice',
        'description' => 'description',
        'dormitory_id' => 'dormitoryId',

        // extra
        'images' => 'images',
        'remove_images' => 'removeImages',
    ];

    public ?string $name;

    public ?float $size;

    public ?float $rentalPrice;

    public ?string $description;

    public ?string $dormitoryId;

    public ?array $images;

    public ?array $removeImages;

    protected array $except = ['images', 'removeImages'];
}
