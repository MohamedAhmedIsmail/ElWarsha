<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Auth\RegisterUserData;
use App\DTOs\Auth\UpdateProfileData;
use App\Enums\UserStatus;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function create(RegisterUserData $data): User
    {
        return User::query()->create([
            'name' => $data->name,
            'phone' => $data->phone,
            'email' => $data->email,
            'password' => Hash::make($data->password),
            'role' => $data->role,
            'status' => UserStatus::Active,
        ]);
    }

    public function findByPhone(string $phone): ?User
    {
        return User::query()
            ->where('phone', $phone)
            ->first();
    }

    public function updateProfile(User $user, UpdateProfileData $data): User
    {
        $user->forceFill($data->toArray())->save();

        return $user->refresh();
    }
}
