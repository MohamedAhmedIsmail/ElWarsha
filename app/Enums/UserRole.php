<?php

namespace App\Enums;

enum UserRole: string
{
    case Customer = 'customer';
    case WorkshopOwner = 'workshop_owner';
    case Provider = 'provider';
    case Admin = 'admin';
    case SuperAdmin = 'super_admin';

    /**
     * @return array<int, string>
     */
    public static function publicRegistrationValues(): array
    {
        return [
            self::Customer->value,
            self::WorkshopOwner->value,
            self::Provider->value,
        ];
    }
}
