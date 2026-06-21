<?php

namespace App\Http\Services;

use App\DTOs\Room\CreateRoomDTO;
use App\DTOs\Room\GetRoomsDTO;
use App\DTOs\Room\UpdateRoomDTO;
use App\Enums\RoomPhotoTypes;
use App\Models\Room;
use App\Models\RoomMedia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class RoomMediaService
{

    private const UPLOAD_IMAGE_PATH = 'rooms';
    public function __construct(
        protected UploadImageService $uploadImageService
    ) {}

    public function updateRoomMedia(Room $room, array $dataUpdate)
    {

        $existingPhotos = RoomMedia::where('room_id', $room->id)
            ->select('id', 'order', 'file_path')
            ->get();

        $strategies = $this->updateImagesStrategies($existingPhotos, $dataUpdate);

        $itemsToCreate = [];
        $itemsToReplace = [];
        $itemsToReOrder = [];

        $newUploadedPaths = [];
        $oldPathsToDelete = [];

        try {
            $existingPhotosMap = $existingPhotos->keyBy('id');

            if (!empty($strategies['toReplace'])) {
                foreach ($strategies['toReplace'] as $item) {
                    if (isset($existingPhotosMap[$item['id']])) {
                        $oldPathsToDelete[] = $existingPhotosMap[$item['id']]->file_path;
                    }

                    $path = $this->uploadImageService->uploadImage($item['file'], self::UPLOAD_IMAGE_PATH);
                    $newUploadedPaths[] = $path;

                    $itemsToReplace[] = [
                        'id'        => (int) $item['id'],
                        'room_id'   => $room->id,
                        'file_path' => $path,
                        'file_name' => $item['file']->getClientOriginalName(),
                        'file_type' => $item['file']->getClientMimeType(),
                        'file_size' => $item['file']->getSize(),
                        'type'      => $item['type'] ?? RoomPhotoTypes::PHOTO->value,
                        'order'     => (int) $item['order'],
                    ];
                }
            }


            if (!empty($strategies['toCreate'])) {
                $now = now();
                foreach ($strategies['toCreate'] as $item) {
                    $path = $this->uploadImageService->uploadImage($item['file'], self::UPLOAD_IMAGE_PATH);
                    $newUploadedPaths[] = $path;

                    $itemsToCreate[] = [
                        'room_id'    => $room->id,
                        'file_path'  => $path,
                        'order'      => (int) $item['order'],
                        'file_name'  => $item['file']->getClientOriginalName(),
                        'file_type'  => $item['file']->getClientMimeType(),
                        'file_size'  => $item['file']->getSize(),
                        'type'       => $item['type'] ?? RoomPhotoTypes::PHOTO->value,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }


            if (!empty($strategies['toDelete'])) {
                foreach ($strategies['toDelete'] as $deleteId) {
                    if (isset($existingPhotosMap[$deleteId])) {
                        $oldPathsToDelete[] = $existingPhotosMap[$deleteId]->file_path;
                    }
                }
            }


            if (!empty($strategies['toReOrder'])) {
                foreach ($strategies['toReOrder'] as $item) {
                    $itemsToReOrder[] = [
                        'id'      => (int) $item['id'],
                        'order'   => (int) $item['order'],
                    ];
                }
            }

            $reOrderSql = '';
            $reOrderBindings = [];

            if (!empty($itemsToReOrder)) {
                $caseStatements = [];
                $reOrderIds = [];

                foreach ($itemsToReOrder as $item) {
                    $caseStatements[] = "WHEN id = ? THEN ?::integer";
                    $reOrderBindings[] = (int) $item['id'];
                    $reOrderBindings[] = (int) $item['order'];
                    $reOrderIds[] = (int) $item['id'];
                }

                $caseSql = implode(' ', $caseStatements);
                $whereInPlaceholders = implode(',', array_fill(0, count($reOrderIds), '?'));

                $reOrderBindings = array_merge($reOrderBindings, $reOrderIds);

                $reOrderSql = "UPDATE room_media SET \"order\" = CASE $caseSql END WHERE id IN ($whereInPlaceholders)";
            }

            DB::transaction(function () use ($strategies, $itemsToCreate, $itemsToReplace, $reOrderSql, $reOrderBindings) {


                if (!empty($strategies['toDelete'])) {
                    RoomMedia::whereIn('id', $strategies['toDelete'])->delete();
                }
                dd($strategies, $reOrderSql, $reOrderBindings);
                if ($reOrderSql !== '') {
                    DB::update($reOrderSql, $reOrderBindings);
                }


                if (!empty($itemsToReplace)) {
                    RoomMedia::upsert(
                        $itemsToReplace,
                        ['id'],
                        ['file_path', 'file_name', 'file_type', 'file_size', 'type', 'order']
                    );
                }


                if (!empty($itemsToCreate)) {
                    RoomMedia::insert($itemsToCreate);
                }
            });

            if (!empty($oldPathsToDelete)) {
                $this->uploadImageService->deleteImagesByPath($oldPathsToDelete);
            }
        } catch (Exception $e) {

            if (!empty($newUploadedPaths)) {
                $this->uploadImageService->deleteImagesByPath($newUploadedPaths);
            }

            report($e);
            throw $e;
        }
    }

    /**
     * Summary of updateImagesStrategies
     * @param Collection<int, RoomMedia> $existingPhotos
     * @param array $dataUpdate
     * @return array{toCreate: array, toDelete: array, toReOrder: array, toReplace: array}
     */
    private function updateImagesStrategies(Collection $existingPhotos, array $dataUpdate)
    {
        if (empty($dataUpdate)) {
            return [
                'toCreate'  => [],
                'toReplace' => [],
                'toDelete'  => [],
                'toReOrder' => [],
            ];
        }


        $inputCollection = collect($dataUpdate);


        $currentOrders = $existingPhotos->pluck('order', 'id');


        $toCreate = $inputCollection->filter(function ($item) {
            return empty($item['id']) && !empty($item['file']);
        })->values()->all();


        $toReplace = $inputCollection->filter(function ($item) {
            return !empty($item['id']) && !empty($item['file']);
        })->values()->all();


        $toReOrder = $inputCollection->filter(function ($item) use ($currentOrders) {
            return !empty($item['id'])
                && empty($item['file'])
                && isset($currentOrders[$item['id']])
                && (int) $currentOrders[$item['id']] !== (int) $item['order'];
        })->values()->all();


        $toDelete = $existingPhotos->pluck('id')
            ->diff($inputCollection->pluck('id'))
            ->values()
            ->all();

        return [
            'toCreate'  => $toCreate,
            'toReplace' => $toReplace,
            'toDelete'  => $toDelete,
            'toReOrder' => $toReOrder,
        ];
    }
}
