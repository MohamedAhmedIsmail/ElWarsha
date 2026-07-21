<?php

namespace App\Http\Controllers;

use App\Http\Requests\Notification\StoreDeviceTokenRequest;
use App\Http\Resources\DeviceTokenResource;
use App\Http\Resources\NotificationResource;
use App\Models\User;
use App\Services\NotificationService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private readonly NotificationService $notificationService)
    {
    }

    public function storeDeviceToken(StoreDeviceTokenRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Device token stored successfully.', [
            'device_token' => new DeviceTokenResource($this->notificationService->storeDeviceToken($user, $request->toDto())),
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Notifications retrieved successfully.', [
            'items' => NotificationResource::collection($this->notificationService->listForUser($user)),
        ]);
    }

    public function markRead(int $notification, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Notification marked as read successfully.', [
            'notification' => new NotificationResource($this->notificationService->markRead($user, $notification)),
        ]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Notifications marked as read successfully.', [
            'updated_count' => $this->notificationService->markAllRead($user),
        ]);
    }
}
