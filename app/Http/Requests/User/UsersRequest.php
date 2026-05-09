<?php

namespace App\Http\Requests\User;

use App\Enums\UserStatus;
use App\Traits\HasPaginationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsersRequest extends FormRequest
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
            'email' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',

            'status' => ['nullable', Rule::enum(UserStatus::class)],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string'],

            'ward_code' => 'nullable|integer',
            'province_code' => 'nullable|integer',
            'detail_address' => 'nullable|string|max:500',

            'sort_by' => ['nullable', Rule::in(['name', 'email', 'created_at'])],

            ...$this->paginationRules(),
        ];
    }
}
