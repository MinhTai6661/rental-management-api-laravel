<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => ['required', 'string'],
            'role' => ['nullable'],
            'name' => ['required', 'string'],
            'phone' => ['nullable', 'string'],
            'avatar' => ['nullable', 'string'],
            'detail_address' => ['nullable', 'string'],
            'ward_code' => ['nullable', 'integer', 'exists:wards,id'],
        ];
    }
}
