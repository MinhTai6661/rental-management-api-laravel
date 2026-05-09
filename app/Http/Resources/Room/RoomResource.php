<?php

namespace App\Http\Resources\Room;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class RoomResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'size' => $this->size,
            'rental_price' => $this->rental_price,
            'status' => $this->status,
            'dormitory_id' => $this->dormitory_id,
            'dormitory_name' => $this->dormitory?->name,
            'description' => $this->description,
            'detail_address' => $this->dormitory?->address,
            'ward_code' => $this->dormitory?->ward_code,
            'ward_name' => $this->whenLoaded('dormitory', function () {
                return $this->dormitory?->ward?->name;
            }),
            'province_name' => $this->whenLoaded('dormitory', function () {
                return $this->dormitory?->ward?->province?->name;
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
