<?php

namespace App\DTOs\Auth;

use App\Enums\UserRole;

final readonly class RegisterUserData
{
    public function __construct(
        public string $name,
        public string $phone,
        public ?string $email,
        public string $password,
        public UserRole $role,
    ) {
    }
}
