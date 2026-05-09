<?php

namespace App\Models;

use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(RoomFactory::class)]
class RoomMedia extends Model
{
    use HasFactory;
    protected $fillable = [
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'order',
        'type',

        'room_id',
    ];

    protected $casts = [
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }
}
