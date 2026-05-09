<?php

namespace App\Http\Resources\Room;

use App\Http\Resources\BaseCollection;
use Illuminate\Http\Request;

class RoomCollection extends BaseCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
