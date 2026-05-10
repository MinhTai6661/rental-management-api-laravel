<?php

namespace App\Http\Requests\Room;

use App\Enums\RoomStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateRoomRequest extends FormRequest
{
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
            'name' => [
                'nullable',
                Rule::unique('rooms')->where(function ($query) {
                    return $query->where('dormitory_id', $this->dormitory_id);
                }),
            ],
            'size' => 'nullable|numeric|min:0',
            'rental_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'dormitory_id' => 'nullable|numeric',
            'status' => [
                'nullable',
                new Enum(RoomStatus::class)
            ],

            // extra
            'deleted_image_ids' => ['nullable', 'array'],
            'deleted_image_ids.*' => [
                'integer',
            ],

            'images' => ['nullable', 'array', 'max:5'],

            'images.*.id' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    if (in_array($value, $this->input('deleted_image_ids', []))) {
                        $fail("ID {$value} đang nằm trong danh sách xóa, không thể sắp xếp.");
                    }
                },
            ],

            'images.*.file' => [
                'required_if:images.*.id,null',
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048'
            ],

            'images.*.file_name' => [
                'required_if:images.*.id,null',
                'nullable',
                'string'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Tên phòng này đã tồn tại trong dãy trọ này, vui lòng chọn tên khác.',
        ];
    }
}
