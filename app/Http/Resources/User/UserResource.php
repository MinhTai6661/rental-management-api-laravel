<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

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
            'id' => $this->id,

            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->map(function ($role) {
                    return [
                        'name' => $role->name,
                        'display_name' => $role->display_name,
                    ];
                });
            }),
            'avatar' => $this->avatar,
            'detail_address' => $this->detail_address,

            'ward_name' => $this->whenLoaded('ward', function () {
                return $this->ward_name;
            }),

            'province_name' => $this->whenLoaded('ward', function () {
                return $this->when($this->ward->relationLoaded('province'), function () {
                    return $this->province_name;
                });
            }),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
