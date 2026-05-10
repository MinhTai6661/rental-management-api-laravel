<?php

namespace App\Models;

use App\Traits\MediaUrl;
use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(RoomFactory::class)]
class RoomMedia extends Model
{
    use HasFactory, MediaUrl;
    protected $fillable = [
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'order',
        'type',

        'room_id',
    ];

    protected $casts = [];

    public function filePath(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $this->getMediaUrl($value),
        );
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }
}
