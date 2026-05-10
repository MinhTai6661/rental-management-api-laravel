<?php

namespace App\Http\Services;

use App\DTOs\Room\CreateRoomDTO;
use App\DTOs\Room\GetRoomsDTO;
use App\DTOs\Room\UpdateRoomDTO;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class RoomMediaService
{
    public function __construct(
        protected UploadImageService $uploadImageService
    ) {}

    public function updateRoomMedia(Room $room, UpdateRoomDTO $dto)
    {
        try {
            

            DB::beginTransaction();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
