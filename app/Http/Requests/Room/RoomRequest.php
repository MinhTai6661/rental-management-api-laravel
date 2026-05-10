<?php

namespace App\Http\Requests\Room;

use App\Enums\RoomStatus;
use App\Traits\HasPaginationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class RoomRequest extends FormRequest
{
    use HasPaginationRules;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'size_from' => 'nullable|numeric|min:0',
            'size_to' => 'nullable|numeric|min:0',
            'rental_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => ['nullable', new Enum(RoomStatus::class)],
            'dormitory_id' => 'nullable|numeric',
            'ward_code' => 'nullable|numeric',
            'province_code' => 'nullable|numeric',
            'sort_by' => ['nullable', Rule::in(['name', 'size', 'rental_price', 'created_at'])],

            ...$this->paginationRules(),
        ];
    }
}
