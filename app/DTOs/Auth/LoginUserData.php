<?php

namespace App\DTOs\Auth;

final readonly class LoginUserData
{
    public function __construct(
        public string $phone,
        public string $password,
    ) {
    }
}
