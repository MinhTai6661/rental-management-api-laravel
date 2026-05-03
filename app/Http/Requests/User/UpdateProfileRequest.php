<?php

namespace App\Http\Requests\User;

use App\Enums\Direction;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Traits\HasPaginationRules;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;

class UpdateProfileRequest extends FormRequest
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
            'name'            => 'nullable|string|max:255',
            'email'           => 'nullable|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'ward_code'         => 'nullable|integer',
            'detail_address'  => 'nullable|string|max:500',
        ];
    }
}
