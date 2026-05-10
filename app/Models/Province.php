<?php

namespace App\Models;

use Database\Factories\ProvinceFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = [
        'code',
        'name',
        'division_type',
        'codename',
    ];

    public function wards()
    {
        return $this->hasMany(Ward::class, 'province_code', 'code');
    }
}
