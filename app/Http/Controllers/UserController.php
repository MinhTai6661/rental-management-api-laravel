<?php

namespace App\Http\Controllers;

use App\DTOs\User\GetAllUserDTO;
use App\DTOs\User\UpdateProfileDTO;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UsersRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Http\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    use ApiResponse;
    public function __construct() {}

    public function users(UsersRequest $request, UserService $userService)
    {

        $dto = GetAllUserDTO::fromRequest($request);
        $users = $userService->getAllUser($dto);

        return (new UserCollection($users))->additional([
            'message' => 'Danh sách người dùng',
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request, UserService $userService)
    {
        $user = $request->user();
        $data = UpdateProfileDTO::fromRequestPartial($request);
        $updatedUser = $userService->updateProfile($user, $data);

        return (new UserResource($updatedUser))->additional([
            'message' => 'Cập nhật thông tin người dùng thành công',
        ]);
    }
}
