<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseResource;
use Faker\Provider\Base;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;


class UserResource extends BaseResource
{


    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->whenNotNull($this->id),

            'name'           => $this->whenNotNull($this->name),
            'email'          => $this->whenNotNull($this->email),
            'phone'          => $this->whenNotNull($this->phone),
            'role'           => $this->whenNotNull($this->role),
            'avatar'         => $this->whenNotNull($this->avatar),
            'detail_address' => $this->whenNotNull($this->detail_address),

            'ward_name'      => $this->whenLoaded('ward', function () {
                return $this->ward_name;
            }),

            'province_name'  => $this->whenLoaded('ward', function () {
                return $this->when($this->ward->relationLoaded('province'), function () {
                    return $this->province_name;
                });
            }),

            'created_at'     => $this->whenNotNull($this->created_at?->format('Y-m-d H:i:s')),
            'updated_at'     => $this->whenNotNull($this->updated_at?->format('Y-m-d H:i:s')),
        ];
    }
}
