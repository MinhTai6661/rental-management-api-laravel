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

    public function createRoom(User $user, CreateRoomRequest $room): Response
    {
        $isOwner = $this->isDormitoryOwner($user->id, $room->dormitory_id);

        $condition = $isOwner;
        if (!$condition) {
            return Response::denyWithStatus(403);
        }

        return   Response::allow();
    }

    private function isOwnerResource(string $userId, Room $room, array $dataUpdate): bool
    {
        $query = $room->newQuery()->where('id', $room->id);

        $query->whereHas('dormitory', function ($q) use ($userId) {
            $q->where('landlord_id', $userId);
        });

        if (!empty($dataUpdate['images'])) {
            $existingIds = collect($dataUpdate['images'])
                ->pluck('id')
                ->filter()
                ->toArray();

            if (!empty($existingIds)) {
                $count = $room->media()->whereIn('id', $existingIds)->count();
                if ($count !== count($existingIds)) return false;
            }
        }

        if (!empty($dataUpdate['deleted_image_ids'])) {
            $deletedIds = $dataUpdate['deleted_image_ids'];

            $countDeleted = $room->media()->whereIn('id', $deletedIds)->count();
            if ($countDeleted !== count($deletedIds)) return false;
        }

        return $query->exists();
    }
    public function updateRoom(User $user, Room $room, array $dataUpdate): Response
    {
        $isOwnerResource = $this->isOwnerResource($user->id, $room, $dataUpdate);
        $condition = $isOwnerResource;

        if (!$condition) {
            return Response::denyWithStatus(403);
        }
        return Response::allow();
    }
}
