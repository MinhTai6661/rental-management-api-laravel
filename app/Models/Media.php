<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'mediable_id',
        'mediable_type',
        'order',
    ];

    public function mediable()
    {
        return $this->morphTo();
    }
}
