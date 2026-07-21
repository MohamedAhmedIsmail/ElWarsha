<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Notification\StoreDeviceTokenData;
use App\Models\DeviceToken;
use App\Repositories\Contracts\DeviceTokenRepositoryInterface;

class DeviceTokenRepository implements DeviceTokenRepositoryInterface
{
    public function storeForUser(int $userId, StoreDeviceTokenData $data): DeviceToken
    {
        return DeviceToken::query()->updateOrCreate(
            [
                'user_id' => $userId,
                'token' => $data->token,
            ],
            [
                'platform' => $data->platform,
                'device_name' => $data->deviceName,
            ]
        );
    }
}
