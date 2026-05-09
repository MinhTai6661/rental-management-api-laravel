<?php

namespace App\Http\Controllers;

use App\DTOs\User\GetAllUserDTO;
use App\DTOs\User\UpdateProfileDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UsersRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\User\UserResource;
use App\Http\Services\UserService;
use App\Models\User;
use App\Traits\ApiResponse;
use Gate;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct() {}

    public function users(UsersRequest $request, UserService $userService)
    {
        $dto = GetAllUserDTO::fromRequest($request);
        $users = $userService->getAllUsers($dto);

        return UserResource::collection($users)->additional([
            'message' => __('general.fetch_success'),
        ]);
    }
    public function updateProfile(UpdateProfileRequest $request, UserService $userService)
    {
        $user = $request->user();
        $data = UpdateProfileDTO::fromRequestPartial($request);
        $updatedUser = $userService->updateProfile($user, $data);

        return (new UserResource($updatedUser))->additional([
            'message' => __('general.update_success'),
        ]);
    }

    public function deleteUser(User $user, UserService $userService) // Laravel tự findOrFail ở đây
    {
        Gate::authorize('delete', $user);
        $userService->deleteUser($user);

        return (new BaseResource(null))->additional([
            'message' => __('general.delete_success'),
        ]);
    }
}
