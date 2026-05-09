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
        return $this->attributes['email_verified_at'] !== null;
    }

    public function hasSuperAdmin(): bool
    {
        return $this->roles->contains('name', UserRole::SUPER_ADMIN->value);
    }

    public function hasAdmin(): bool
    {
        return $this->roles->contains('name', UserRole::ADMIN->value);
    }

    public function hasLandlord(): bool
    {
        return $this->roles->contains('name', UserRole::LANDLORD->value);
    }

    public function hasTenant(): bool
    {
        return $this->roles->contains('name', UserRole::TENANT->value);
    }

    public function isOwner(string $userId): bool
    {
        return $this->attributes['id'] === $userId;
    }
}
