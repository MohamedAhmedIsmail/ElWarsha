<?php

namespace App\Repositories\Contracts;

use App\Models\Notification;

interface NotificationRepositoryInterface
{
    /**
     * @param array<string, mixed>|null $data
     */
    public function create(int $userId, string $title, string $body, string $type, ?array $data = null): Notification;
}
