<?php

namespace App\Http\Controllers;

use App\DTOs\Auth\authenticateDTO;
use App\DTOs\User\CreateUserDTO;
use App\Enums\UserStatus;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Services\AuthService;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Http\Services\MailService;
use App\Http\Services\UserService;
use App\Http\Services\UserVerificationService;
use App\Models\User;
use App\Models\UserOauth;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use ApiResponse;
    public function __construct() {}
    public function register(RegisterRequest $request, UserService $userService, UserVerificationService $userVerificationService, MailService $mailService)
    {
        $dto = CreateUserDTO::fromRequestPartial($request);
        $user = $userService->createUser($dto);
        $token = $userVerificationService->createToken($user->id);
        $mailService->sendRegisterConfirmation($user, $token);

        return  $user->toResource(UserResource::class)->additional([
            'message' => 'Đăng ký thành công. Vui lòng kiểm tra email để xác nhận tài khoản.',
        ]);
    }


    public function login(LoginRequest $request, AuthService $authService)
    {
        $dto = authenticateDTO::fromRequest($request);
        $user = $authService->authenticate($dto);
        $token = $authService->generateTokenForUser($user);

        return (new UserResource($user))->additional([
            'token'   => $token,
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

        /** @var \Laravel\Socialite\Two\GoogleProvider  */
        $driver = Socialite::driver('google');
        return $driver
            ->stateless()
            ->with(['state' => $state])
            ->redirect();
    }

    public function resetToken(Request $request, UserVerificationService $userVerificationService)
    {
        $currentToken = $request->user()->currentAccessToken();
        $user = $request->user();
        $userVerificationService->resetToken($user->id, $currentToken);
        $token = $userVerificationService->createToken($user->id);

        return (new BaseResource(null))->additional([
            'message' => 'Token xác nhận mới đã được tạo. Vui lòng kiểm tra email của bạn.',
            'token' => $token,
        ]);
    }
    public function handleGoogleCallback(Request $request, AuthService $authService)
    {
        try {
            $target = $authService->extractState($request->input('state'), 'redirect') ?? 'dashboard123123';
            /** @var \Laravel\Socialite\Two\GoogleProvider  */
            $driver = Socialite::driver('google');
            $googleUser = $driver->stateless()->user();
            // dd($googleUser);
            ['user' => $user, 'token' => $token] = $authService->googleLoginOrRegister($googleUser);

            return redirect()->to(config('app.CLIENT_URL') . "/{$target}?token={$token}");
        } catch (\Exception $e) {
            return redirect()->to(config('app.CLIENT_URL') . "/login?error=social_failed");
        }
    }
}
