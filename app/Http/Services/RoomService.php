<?php

namespace App\Http\Services;

use App\DTOs\Room\CreateRoomDTO;
use App\DTOs\Room\GetRoomsDTO;
use App\DTOs\Room\UpdateRoomDTO;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class RoomService
{
    public function __construct(
        protected UserService $userService,
        protected MailService $mailService,
        protected UserVerificationService $userVerificationService,
        protected UploadImageService $uploadImageService
    ) {}

    public function getRooms(GetRoomsDTO $dto, array $columns = ['*'], array $relations = [])
    {
        $query = Room::select($columns)
            ->with(array_merge($relations, ['dormitory']));

        if ($dto->name) {
            $query->where('name', 'like', '%' . $dto->name . '%');
        }
        if ($dto->sizeFrom && $dto->sizeTo) {
            $query->where('size', '>=', $dto->sizeFrom)->where('size', '<=', $dto->sizeTo);
        }

        if ($dto->rentalPrice) {
            $query->where('rental_price', $dto->rentalPrice);
        }

        if ($dto->dormitoryId) {
            $query->where('dormitory_id', $dto->dormitoryId);
        }

        if ($dto->status) {
            $query->where('status', $dto->status);
        }

        if ($dto->sortBy && $dto->direction) {
            $query->orderBy($dto->sortBy, $dto->direction);
        }

        return $query->paginate($dto->perPage || 10);
    }

    public function getRoomsByUserId(string $userId, GetRoomsDTO $dto, array $columns = ['*'], array $relations = [])
    {
        $query = Room::select($columns)
            ->whereHas('dormitory', function ($query) use ($userId) {
                $query->where('landlord_id', $userId);
            })
            ->with(array_merge($relations, ['dormitory']));

        if ($dto->name) {
            $query->where('name', 'like', '%' . $dto->name . '%');
        }
        if ($dto->sizeFrom && $dto->sizeTo) {
            $query->where('size', '>=', $dto->sizeFrom)->where('size', '<=', $dto->sizeTo);
        }

        if ($dto->rentalPrice) {
            $query->where('rental_price', $dto->rentalPrice);
        }

        if ($dto->dormitoryId) {
            $query->where('dormitory_id', $dto->dormitoryId);
        }

        if ($dto->wardCode) {
            $query->whereHas('dormitory', function ($query) use ($dto) {
                $query->where('ward_code', $dto->wardCode);
            });
        }

        if ($dto->provinceCode) {
            $query->whereHas('dormitory', function ($query) use ($dto) {
                $query->whereHas('ward', function ($query) use ($dto) {
                    $query->where('province_code', $dto->provinceCode);
                });
            });
        }

        if ($dto->status) {
            $query->where('status', $dto->status);
        }

        if ($dto->sortBy && $dto->direction) {
            $query->orderBy($dto->sortBy, $dto->direction);
        }

        return $query->paginate($dto->perPage);
    }

    public function createRoom(CreateRoomDTO $dto)
    {
        $uploadedImages = [];

        try {
            if (! empty($dto->images)) {
                $uploadedImages = $this->uploadImageService->uploadImages($dto->images, 'rooms');
            }
        } catch (\Exception $e) {
            if (! empty($uploadedImages)) {
                $this->uploadImageService->deleteImages($uploadedImages);
            }
            throw new \Exception(__('upload_error'));
        }

        DB::beginTransaction();
        try {
            $room = Room::create($dto->toArray());

            if (! empty($uploadedImages)) {
                $room->media()->createMany($uploadedImages);
            }

            DB::commit();

            return $room->load('media');
        } catch (\Exception $e) {
            DB::rollBack();
            if (! empty($uploadedImages)) {
                $this->uploadImageService->deleteImages($uploadedImages);
            }

            throw new \Exception(__('upload_error'));
        }
    }

    private function updateRoomImages(Room $room, array $dataUpdate)
    {
        dd('ahihi', $dataUpdate);
    }

    public function updateRoom(Room $room, UpdateRoomDTO $dto): Room
    {
        try {
            dd($dto);
            if (!empty($dto->images) && !empty($dto->deletedImageIds)) {
                $this->updateRoomImages($room, $dto->images);
            }
            dd($dto->toArray());
            $dataUpdate = $dto->toArray();
            $room->update($dataUpdate);
            return $room;
        } catch (\Exception $e) {
            throw new \Exception(__('room.update_failed'));
        }
    }
}
