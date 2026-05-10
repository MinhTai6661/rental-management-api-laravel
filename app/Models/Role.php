<?php

namespace App\Models;

use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(RoleFactory::class)]
class Role extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'display_name', 'level'];

    protected $casts = ['level' => 'integer'];
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_has_roles', 'role_id', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }
}
