<?php

namespace App\Http\Resources\Role;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class RoleResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->whenNotNull($this->id),
            'name' => $this->whenNotNull($this->name),
            'permissions' => $this->whenNotNull($this->permissions->pluck('name')),
            'display_name' => $this->whenNotNull($this->display_name),
            'created_at' => $this->whenNotNull($this->created_at?->format('Y-m-d H:i:s')),
            'updated_at' => $this->whenNotNull($this->updated_at?->format('Y-m-d H:i:s')),
        ];
    }
}
