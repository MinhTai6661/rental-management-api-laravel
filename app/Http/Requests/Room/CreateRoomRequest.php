<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRoomRequest extends FormRequest
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
                'required',
                Rule::unique('rooms')->where(function ($query) {
                    return $query->where('dormitory_id', $this->dormitory_id);
                }),
            ],
            'size' => 'nullable|numeric|min:0',
            'rental_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'dormitory_id' => 'required|numeric|exists:dormitories,id',
            'images' => 'nullable|max:2048',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Tên phòng này đã tồn tại trong dãy trọ này, vui lòng chọn tên khác.',
        ];
    }
}
