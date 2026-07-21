<?php

namespace App\Http\Controllers;

use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\User;
use App\Services\ReviewService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(private readonly ReviewService $reviewService)
    {
    }

    public function store(StoreReviewRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Review created successfully.', [
            'review' => new ReviewResource($this->reviewService->create($user, $request->toDto())),
        ], 201);
    }

    public function mine(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Reviews retrieved successfully.', [
            'items' => ReviewResource::collection($this->reviewService->listForUser($user)),
        ]);
    }

    public function update(int $review, UpdateReviewRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Review updated successfully.', [
            'review' => new ReviewResource($this->reviewService->update($user, $review, $request->toDto())),
        ]);
    }

    public function destroy(int $review, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->reviewService->delete($user, $review);

        return ApiResponse::success('Review deleted successfully.');
    }
}
