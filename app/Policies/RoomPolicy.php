<?php

namespace App\Policies;

use App\Http\Requests\Room\CreateRoomRequest;
use App\Models\Dormitory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RoomPolicy
{
    private function isDormitoryOwner(string $userId, int $dormitoryId): bool
    {
        return Dormitory::where('id', $dormitoryId)
            ->where('landlord_id', $userId)
            ->exists();
    }

    public function createRoom(User $user, CreateRoomRequest $room): Response
    {
        $isOwner = $this->isDormitoryOwner($user->id, $room->dormitory_id);

        $condition = $isOwner;

        return $condition ? Response::allow() : Response::denyWithStatus(403);
    }

    public function updateRoom(User $user, CreateRoomRequest $room): Response
    {
        $isOwner = $this->isDormitoryOwner($user->id, $room->dormitory_id);

        $condition = $isOwner;

        return $condition ? Response::allow() : Response::denyWithStatus(403);
    }
}
