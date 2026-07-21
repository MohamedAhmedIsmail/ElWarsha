<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscription\RequestSubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\User;
use App\Services\WorkshopSubscriptionService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkshopSubscriptionController extends Controller
{
    public function __construct(private readonly WorkshopSubscriptionService $workshopSubscriptionService)
    {
    }

    public function show(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $subscription = $this->workshopSubscriptionService->current($user);

        return ApiResponse::success('Workshop subscription retrieved successfully.', [
            'subscription' => $subscription ? new SubscriptionResource($subscription) : null,
        ]);
    }

    public function request(RequestSubscriptionRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Subscription request submitted successfully.', [
            'subscription' => new SubscriptionResource($this->workshopSubscriptionService->request($user, $request->toDto())),
        ], 201);
    }
}
