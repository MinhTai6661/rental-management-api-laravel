<?php

namespace App\Http\Controllers;

use App\DTOs\Room\CreateRoomDTO;
use App\DTOs\Room\GetRoomsDTO;
use App\DTOs\Room\UpdateRoomDTO;
use App\Http\Requests\Room\CreateRoomRequest;
use App\Http\Requests\Room\RoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Http\Resources\Room\RoomCollection;
use App\Http\Resources\Room\RoomResource;
use App\Http\Services\RoomService;
use App\Models\Room;
use Gate;

class RoomController extends Controller
{
    public function rooms(RoomRequest $request, RoomService $roomService)
    {
        $dto = GetRoomsDTO::fromRequest($request);
        $rooms = $roomService->getRoomsByUserId($request->user()->id, $dto);

        return (new RoomCollection($rooms))->additional([
            'message' => __('room.get_list_success'),
        ]);
    }

    public function createRoom(CreateRoomRequest $request, RoomService $roomService)
    {
        Gate::authorize('createRoom', [Room::class, $request]);

        $dto = CreateRoomDTO::fromRequestPartial($request);
        $room = $roomService->createRoom($dto);

        return (new RoomResource($room))->additional(['message' => __('room.create_success')]);
    }

    public function updateRoom(Room $room, UpdateRoomRequest $updateRoomRequest, RoomService $roomService)
    {
        Gate::authorize('updateRoom', [Room::class, $room, $updateRoomRequest->validated()]);
        $dto = UpdateRoomDTO::fromRequestPartial($updateRoomRequest);
        $room = $roomService->updateRoom($room, $dto);

        return (new RoomResource($room))->additional(['message' => __('room.update_success')]);
    }

}
