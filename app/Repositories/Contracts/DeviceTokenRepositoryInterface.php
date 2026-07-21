<?php

namespace App\Repositories\Contracts;

use App\DTOs\Notification\StoreDeviceTokenData;
use App\Models\DeviceToken;

interface DeviceTokenRepositoryInterface
{
    public function storeForUser(int $userId, StoreDeviceTokenData $data): DeviceToken;
}
