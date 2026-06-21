<?php

namespace App\Traits\User;

use App\Enums\UserRole;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property-read Collection|Role[] $roles
 * @property string $id
 */
trait HasVerification
{
    public function isVerified(): bool
    {
        return $this->roles()->where('name', UserRole::SUPER_ADMIN->value)->exists();
    }

    public function hasRole(array $role): bool
    {
        return $this->roles()->select('name')
            ->whereIn('name', $role)
            ->exists();
    }

    public function hasSuperAdmin(): bool
    {
        return $this->hasRole([UserRole::SUPER_ADMIN->value]);
    }

    public function hasAdmin(): bool
    {
        return $this->hasRole([UserRole::ADMIN->value]);
    }

    public function hasLandlord(): bool
    {
        return $this->hasRole([UserRole::LANDLORD->value]);
    }

    public function hasTenant(): bool
    {
        return $this->hasRole([UserRole::TENANT->value]);
    }

    public function isOwner(string $userId): bool
    {
        return $this->attributes['id'] === $userId;
    }
}
