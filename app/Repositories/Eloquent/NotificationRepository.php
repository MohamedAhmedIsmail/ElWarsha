<?php

namespace App\Repositories\Eloquent;

use App\Models\Notification;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function listForUser(int $userId): Collection
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->latest('id')
            ->get();
    }

    public function findForUser(int $userId, int $notificationId): ?Notification
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->whereKey($notificationId)
            ->first();
    }

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

    public function markRead(Notification $notification): Notification
    {
        if ($notification->read_at === null) {
            $notification->forceFill(['read_at' => now()])->save();
        }

        return $notification->refresh();
    }

    public function markAllReadForUser(int $userId): int
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
