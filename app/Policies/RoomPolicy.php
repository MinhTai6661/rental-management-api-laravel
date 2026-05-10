<?php

namespace App\Policies;

use App\Http\Requests\Room\CreateRoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Models\Dormitory;
use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RoomPolicy
{

    public function before(User $user, string $ability)
    {
        if ($user->hasSuperAdmin()) {
            return Response::allow();
        }
    }
    private function isDormitoryOwner(string $userId, int | null $dormitoryId): bool
    {
        if ($dormitoryId === null) {
            return false;
        }
        return Dormitory::where('id', $dormitoryId)
            ->where('landlord_id', $userId)
            ->exists();
    }

    private function isRoomOwner(string $userId, Room $room): bool
    {
        $room->load('dormitory');
        return $room->dormitory->landlord_id === $userId;
    }

    public function createRoom(User $user, CreateRoomRequest $room): Response
    {
        $isOwner = $this->isDormitoryOwner($user->id, $room->dormitory_id);

        $condition = $isOwner;
        if (!$condition) {
            return Response::denyWithStatus(403);
        }

        return   Response::allow();
    }

    public function updateRoom(User $user, Room $room): Response
    {
        $isOwner = $this->isRoomOwner($user->id, $room);
        $condition = $isOwner;
        if (!$condition) {
            return Response::denyWithStatus(403);
        }
        return Response::allow();
    }
}
