<?php

namespace App\Http\Services;

use App\DTOs\Auth\authenticateDTO;
use App\DTOs\User\createUserDTO;
use App\Enums\ProviderEnum;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\PasswordResetTokens;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{

    public function __construct(
        protected UserService $userService,
        protected MailService $mailService
    ) {}
    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(authenticateDTO $request): User
    {
        $user = User::where("email", $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['Thông tin đăng nhập không chính xác.']);
        }
        return $user;
    }

    public function generateTokenForUser(User $user)
    {
        $token = $user->createToken('auth_token');

        return $token->plainTextToken;
    }

    public function logout(User $user)
    {
        $user->tokens()->delete();
    }

    public function forgotPassword(string $email): PasswordResetTokens
    {
        $user = $this->userService->getUserByEmail($email);

        $tokenReset =  PasswordResetTokens::updateOrCreate(
            ['user_id' => $user->id],
            [
                'token' => Str::random(60),
                'expires_at' => now()->addMinutes(60),
            ]
        );

        $this->mailService->sendPasswordReset($user, $tokenReset->token);
        return $tokenReset;
    }

    public function resetPassword(string $token, string $newPassword): void
    {
        $token = PasswordResetTokens::where('token', $token)->first();
        if (! $token || $token->expires_at->isPast()) {
            throw ValidationException::withMessages(['Token không hợp lệ hoặc đã hết hạn.']);
        }

        $user = $token->user;

        if (! $user) {
            throw ValidationException::withMessages(['Người dùng không tồn tại.']);
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        $token->delete();
    }

    private function createOauthUser(\Laravel\Socialite\Contracts\User $googleUser): User
    {
        DB::beginTransaction();

        $path = 'avatars/' . $googleUser->getId() . '.jpg';
        Storage::disk('public')->put($path, file_get_contents($googleUser->getAvatar()));
        try {
            $userDTO = new createUserDTO([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'avatar' => $path,
                'email_verified_at' => now(),
            ]);


            $user = $this->userService->createUser($userDTO);
            $user->userOauth()->create([
                'provider' => ProviderEnum::GOOGLE->value,
                'provider_id' => $googleUser->getId(),
            ]);
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function googleLoginOrRegister(\Laravel\Socialite\Contracts\User $googleUser)
    {
        $user = $this->userService->getUserByEmail($googleUser->getEmail());

        if ($user && !$user->isVerified()) {
            DB::beginTransaction();
            try {
                $user->password = null;
                $user->email_verified_at = Carbon::now();
                $user->save();

                $user->userOauth()->create([
                    'provider' => ProviderEnum::GOOGLE->value,
                    'provider_id' => $googleUser->getId(),
                ]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }

        if (!$user) {
            $user =  $this->createOauthUser($googleUser);
        }
        $token = $this->generateTokenForUser($user);
        $data = [
            'user' => $user,
            'token' => $token,
        ];
        return $data;
    }
    public function extractState(string $state, string $key): string | null
    {
        parse_str($state, $result);
        return $result[$key] ?? null;
    }
}
