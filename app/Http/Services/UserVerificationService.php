<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserVerificationService
{
    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function createToken(string $user_id): string
    {
        $isExist = UserVerification::where('user_id', $user_id)->first();
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
        if (! $isExist) {
            throw ValidationException::withMessages(['Không tìm thấy thông tin xác thực cho người dùng này.']);
        } else {
            UserVerification::where('user_id', $user_id)->where('token', $currentToken)->update([
                'token' => $token,
                'expires_at' => now()->addHours($hours),
            ]);
        }

        return $token;
    }

    public function confirmEmail(string $token): void
    {
        $verification = UserVerification::where('token', $token)->first();

        if (! $verification || $verification->expires_at->isPast()) {
            throw ValidationException::withMessages(['Token xác nhận không hợp lệ hoặc đã hết hạn.']);
        }

        $user = User::find($verification->user_id);
        if ($user) {
            $user->status = 'active';
            $user->email_verified_at = now();
            $user->save();
        }

        $verification->delete();
    }
}
