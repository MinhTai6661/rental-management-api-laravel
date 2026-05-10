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

            if ($dto->images) {
                foreach ($dto->images as $image) {
                    $path = $this->uploadImageService->uploadImage($image, 'room_media');
                    $room->media()->create(['url' => $path]);
                }
            }

            if ($dto->removeImages) {
                foreach ($dto->removeImages as $mediaId) {
                    $media = $room->media()->find($mediaId);
                    if ($media) {
                        $this->uploadImageService->deleteImage($media->url);
                        $media->delete();
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            throw $th;
        }
    }

}
