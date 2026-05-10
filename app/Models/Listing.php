<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'published_at',
        'slug',
        'room_id',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
