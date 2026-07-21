<?php

namespace App\DTOs\Notification;

use App\Enums\DevicePlatform;

class StoreDeviceTokenData
{
    public function __construct(
        public readonly string $token,
        public readonly DevicePlatform $platform,
        public readonly ?string $deviceName,
    ) {
    }
}
