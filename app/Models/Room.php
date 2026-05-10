<?php

namespace App\Models;

use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(RoomFactory::class)]
class Room extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'size',
        'rental_price',
        'description',
        'status',
        'dormitory_id',
    ];

    protected $casts = [
        'size' => 'decimal:2',
        'rental_price' => 'decimal:2',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function listings()
    {
        return $this->hasMany(Listing::class, 'room_id', 'id');
    }

    public function dormitory()
    {
        return $this->belongsTo(Dormitory::class, 'dormitory_id', 'id');
    }

    public function media()
    {
        return $this->hasMany(RoomMedia::class, 'room_id', 'id');
    }
}
