<?php

namespace App\Repositories\Contracts;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

interface NotificationRepositoryInterface
{
    /**
     * @return Collection<int, Notification>
     */
    public function listForUser(int $userId): Collection;

    public function findForUser(int $userId, int $notificationId): ?Notification;

    /**
     * @param array<string, mixed>|null $data
     */
    public function create(int $userId, string $title, string $body, string $type, ?array $data = null): Notification;

    public function markRead(Notification $notification): Notification;

    public function markAllReadForUser(int $userId): int;
}
