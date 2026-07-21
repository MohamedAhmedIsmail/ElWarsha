<?php

namespace App\Repositories\Eloquent;

use App\Models\Notification;
use App\Repositories\Contracts\NotificationRepositoryInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function create(int $userId, string $title, string $body, string $type, ?array $data = null): Notification
    {
        return Notification::query()->create([
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'type' => $type,
            'data' => $data,
        ]);
    }
}
