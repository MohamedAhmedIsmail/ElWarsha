<?php

namespace App\Repositories\Contracts;

use App\DTOs\Auth\RegisterUserData;
use App\DTOs\Auth\UpdateProfileData;
use App\Models\User;

interface UserRepositoryInterface
{
    public function create(RegisterUserData $data): User;

    public function findByPhone(string $phone): ?User;

    public function updateProfile(User $user, UpdateProfileData $data): User;
}
