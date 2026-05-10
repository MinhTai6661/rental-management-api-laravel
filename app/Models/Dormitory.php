<?php

namespace App\Models;

use Database\Factories\DormitoryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(DormitoryFactory::class)]
class Dormitory extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'address',
        'description',
        'landlord_id',
        'ward_code',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class, 'dormitory_id', 'id');
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id', 'id');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_code', 'code');
    }
}
