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
        'status' => 'status',

        // extra
        'images' => 'images',
        'deleted_image_ids' => 'deletedImageIds',
    ];

    public ?string $name;
    public ?float $size;
    public ?float $rentalPrice;
    public ?string $description;
    public ?int $dormitoryId;
    public ?string $status;

    public ?array $images;
    public ?array $deletedImageIds;
    protected array $except = ['images', 'deletedImageIds'];
}
