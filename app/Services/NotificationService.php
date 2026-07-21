<?php

namespace App\Services;

use App\DTOs\Notification\StoreDeviceTokenData;
use App\Models\DeviceToken;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\DeviceTokenRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotificationService
{
    public function __construct(
        private readonly DeviceTokenRepositoryInterface $deviceTokens,
        private readonly NotificationRepositoryInterface $notifications,
    ) {
    }

    public function storeDeviceToken(User $user, StoreDeviceTokenData $data): DeviceToken
    {
        return $this->deviceTokens->storeForUser($user->id, $data);
    }

    /**
     * @return Collection<int, Notification>
     */
    public function listForUser(User $user): Collection
    {
        return $this->notifications->listForUser($user->id);
    }

    public function markRead(User $user, int $notificationId): Notification
    {
        $notification = $this->notifications->findForUser($user->id, $notificationId)
            ?? throw new NotFoundHttpException('Notification not found.');

        return $this->notifications->markRead($notification);
    }

    public function markAllRead(User $user): int
    {
        return $this->notifications->markAllReadForUser($user->id);
    }

    /**
     * @param array<string, mixed>|null $data
     */
    public function createStoredNotification(int $userId, string $title, string $body, string $type, ?array $data = null): Notification
    {
        return $this->notifications->create($userId, $title, $body, $type, $data);
    }
}
