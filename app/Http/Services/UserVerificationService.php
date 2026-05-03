<?php

namespace App\Http\Services;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserVerificationService
{

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function createToken(string $user_id): string
    {
        $token = Str::random(64);
        $hours = (int) config('auth.token_expire_hours', 24);
        UserVerification::create([
            'user_id' => $user_id,
            'token' => $token,
            'expires_at' => now()->addHours($hours),
        ]);

        return $token;
    }

    public function resetToken(string $user_id, $currentToken): string
    {
        $token = Str::random(64);
        $hours = (int) config('auth.token_expire_hours', 24);
        $isExist = UserVerification::where('user_id', $user_id)->where('token', $currentToken)->first();
        if (!$isExist) {
            throw ValidationException::withMessages(['Không tìm thấy thông tin xác thực cho người dùng này.']);
        } else {
            UserVerification::where('user_id', $user_id)->where('token', $currentToken)->update([
                'token' => $token,
                'expires_at' => now()->addHours($hours),
            ]);
        }
        return $token;
    }
}
