<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class ListingPolicy
{
    /**
     * Create a new policy instance.
     */
    public function createListing(User $user): Response
    {
        $condition = true;

        return $condition ? Response::allow() : Response::denyWithStatus(403);
    }

    public function modifyListing(User $user, User $model): Response
    {
        $condition = $user->isOwner($model->id);

        return $condition ? Response::allow() : Response::denyWithStatus(403);
    }

    public function deleteListing(User $user, User $model): Response
    {
        $condition = $user->isOwner($model->id);

        return $condition ? Response::allow() : Response::denyWithStatus(403);
    }

    public function addUserToListing(User $user, User $model): Response
    {
        $condition = $user->isOwner($model->id);

        return $condition ? Response::allow() : Response::denyWithStatus(403);
    }
}
