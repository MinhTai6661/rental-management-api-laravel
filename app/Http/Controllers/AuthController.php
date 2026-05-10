<?php

namespace App\Http\Controllers;

use App\DTOs\Auth\authenticateDTO;
use App\DTOs\User\CreateUserDTO;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\User\UserResource;
use App\Http\Services\AuthService;
use App\Http\Services\MailService;
use App\Http\Services\UserVerificationService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct() {}

    public function register(RegisterRequest $request, AuthService $authService, UserVerificationService $userVerificationService, MailService $mailService)
    {
        $dto = CreateUserDTO::fromRequestPartial($request);
        $user = $authService->register($dto);

        return $user->toResource(UserResource::class)->additional([
            'message' => 'Đăng ký thành công. Vui lòng kiểm tra email để xác nhận tài khoản.',
        ]);
    }

    public function confirmRegister(Request $request, UserVerificationService $userVerificationService)
    {
        $request->validate([
            'token' => 'required|string',
        ]);
        $token = $request->input('token');
        $userVerificationService->confirmEmail($token);

        return (new BaseResource(null))->additional([
            'message' => 'Tài khoản đã được xác nhận thành công. Bạn có thể đăng nhập ngay bây giờ.',
        ]);
    }

    public function login(LoginRequest $request, AuthService $authService)
    {
        $dto = authenticateDTO::fromRequest($request);
        $user = $authService->authenticate($dto);
        $token = $authService->generateTokenForUser($user);

        return (new UserResource($user))->additional([
            'token' => $token,
            'message' => 'Đăng nhập thành công',
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request, AuthService $authService)
    {

        $authService->forgotPassword($request->email);

        return (new BaseResource(null))->additional([
            'message' => 'Nếu email tồn tại trong hệ thống, bạn sẽ nhận được một email hướng dẫn đặt lại mật khẩu.',
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request, AuthService $authService)
    {
        $authService->resetPassword($request->token, $request->password);

        return (new BaseResource(null))->additional([
            'message' => 'Mật khẩu đã được đặt lại thành công.',
        ]);
    }

    public function logout(Request $request, AuthService $authService)
    {
        $user = $request->user();
        $authService->logout($user);

        return (new BaseResource(null))->additional([
            'message' => 'Đăng xuất thành công.',
        ]);
    }

    public function redirectToGoogle(Request $request)
    {
        $params = [
            'redirect' => $request->query('redirect', 'dashboard'),
        ];

        $state = http_build_query($params);

        $driver = Socialite::driver('google');

        return $driver
            ->stateless()
            ->with(['state' => $state])
            ->redirect();
    }

    public function handleGoogleCallback(Request $request, AuthService $authService)
    {
        try {
            $target = $authService->extractStateFromOauth($request->input('state'), 'redirect') ?? 'dashboard123123';
            $driver = Socialite::driver('google');
            $googleUser = $driver->stateless()->user();
            ['user' => $user, 'token' => $token] = $authService->googleLoginOrRegister($googleUser);

            return redirect()->to(config('app.CLIENT_URL')."/{$target}?token={$token}");
        } catch (\Exception $e) {
            return redirect()->to(config('app.CLIENT_URL').'/login?error=social_failed');
        }
    }
}
