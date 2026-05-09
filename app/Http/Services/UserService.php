<?php

namespace App\Http\Services;

use App\DTOs\User\CreateUserDTO;
use App\DTOs\User\GetAllUserDTO;
use App\DTOs\User\UpdateProfileDTO;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function createUser(CreateUserDTO $userDto): User
    {
        $data = $userDto->toArray();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user = User::create($data);

        return $user;
    }

    public function deleteUser(User $user): void
    {
        $user->delete();
    }

    public function getUserByEmail(string $email, array $columns = ['*']): ?User
    {
        $user = User::select($columns)->where('email', $email)->first();

        return $user;
    }

    public function getAllUsers(GetAllUserDTO $dto, array $columns = ['*'], array $relations = []): LengthAwarePaginator
    {

        $query = User::select($columns)->with(array_merge(['ward.province', 'roles'], $relations));

        if ($dto->status) {
            $query->where('status', $dto->status);
        }

        // if ($dto->role) {
        //     $query->where('role', $dto->role);
        // }

        if ($dto->wardCode) {
            $query->where('ward_code', $dto->wardCode);
        } elseif ($dto->provinceCode) {
            $query->whereHas('ward', function ($q) use ($dto) {
                $q->where('province_code', $dto->provinceCode);
            });
        }

        if ($dto->name) {
            $query->where('name', 'like', '%'.$dto->name.'%');
        }

        if ($dto->email) {
            $query->where('email', 'like', '%'.$dto->email.'%');
        }
        // Sorting
        if ($dto->sortBy && $dto->direction) {
            $query->orderBy($dto->sortBy, $dto->direction);
        }

        return $query->paginate($dto->perPage);
    }

    public function getUserById(int $id, array $columns = ['*'], array $relations = []): ?User
    {
        return User::select($columns)->with($relations)->find($id);
    }

    public function updateProfile(User $user, UpdateProfileDTO $data): User
    {
        $user->update($data->toArray());

        // dd($user);
        return $user->load('ward.province');
    }
}
