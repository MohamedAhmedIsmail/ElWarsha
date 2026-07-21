<?php

namespace App\DTOs\Auth;

final readonly class UpdateProfileData
{
    public function __construct(
        public ?string $name,
        public ?string $email,
        public ?string $city,
        public ?string $area,
        public ?string $avatar,
    ) {
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'city' => $this->city,
            'area' => $this->area,
            'avatar' => $this->avatar,
        ], static fn ($value): bool => $value !== null);
    }
}
