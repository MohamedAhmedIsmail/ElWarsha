<?php

namespace App\Repositories\Contracts;

use App\DTOs\Review\StoreReviewData;
use App\DTOs\Review\UpdateReviewData;
use App\Models\Review;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReviewRepositoryInterface
{
    public function listPublishedForWorkshop(Workshop $workshop, int $perPage): LengthAwarePaginator;

    /**
     * @return Collection<int, Review>
     */
    public function listForUser(int $userId): Collection;

    public function findForUser(int $userId, int $reviewId): ?Review;

    public function existsForBooking(int $bookingId, ?int $exceptReviewId = null): bool;

    public function createForUser(int $userId, StoreReviewData $data): Review;

    public function update(Review $review, UpdateReviewData $data): Review;

    public function delete(Review $review): void;

    public function refreshWorkshopRating(Workshop $workshop): Workshop;
}
