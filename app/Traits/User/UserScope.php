<?php

namespace App\Traits\User;

use App\Enums\UserRole;

trait UserScope
{
    /**
     * Summary of scopeVerified
     *
     * @param  mixed  $query
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Summary of scopeByRole
     *
     * @param  mixed  $query
     */
    public function scopeByRole($query, UserRole $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role->value);
        });
    }

    /**
     * Summary of scopeIsSuperAdmin
     *
     * @param  mixed  $query
     */
    public function scopeIsSuperAdmin($query)
    {
        return $this->scopeByRole($query, UserRole::SUPER_ADMIN);
    }

    /**
     * Summary of scopeIsAdmin
     *
     * @param  mixed  $query
     */
    public function scopeIsAdmin($query)
    {
        return $this->scopeByRole($query, UserRole::ADMIN);
    }

    /**
     * Summary of scopeIsLandlord
     *
     * @param  mixed  $query
     */
    public function scopeIsLandlord($query)
    {
        return $this->scopeByRole($query, UserRole::LANDLORD);
    }

    /**
     * Summary of scopeIsTenant
     *
     * @param  mixed  $query
     */
    public function scopeIsTenant($query)
    {
        return $this->scopeByRole($query, UserRole::TENANT);
    }

    public function scopeExcludeCurrentUser($query)
    {
        return $query->whereNot('id', request()->user()->id);
    }
}
