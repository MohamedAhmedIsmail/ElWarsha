<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Review\StoreReviewData;
use App\DTOs\Review\UpdateReviewData;
use App\Enums\ReviewStatus;
use App\Models\Review;
use App\Models\Workshop;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function listPublishedForWorkshop(Workshop $workshop, int $perPage): LengthAwarePaginator
    {
        return $workshop->reviews()
            ->published()
            ->with(['user:id,name,phone,avatar,city,area,role,status,phone_verified_at,last_login_at,created_at'])
            ->latest('id')
            ->paginate($perPage);
    }

    public function listForUser(int $userId): Collection
    {
        return Review::query()
            ->where('user_id', $userId)
            ->with($this->relations())
            ->latest('id')
            ->get();
    }

    public function findForUser(int $userId, int $reviewId): ?Review
    {
        return Review::query()
            ->where('user_id', $userId)
            ->with($this->relations())
            ->whereKey($reviewId)
            ->first();
    }

    public function existsForBooking(int $bookingId, ?int $exceptReviewId = null): bool
    {
        return Review::query()
            ->where('booking_id', $bookingId)
            ->when($exceptReviewId, fn ($query, int $reviewId) => $query->whereKeyNot($reviewId))
            ->exists();
    }

    public function createForUser(int $userId, StoreReviewData $data): Review
    {
        $review = Review::query()->create([
            ...$data->toArray(),
            'user_id' => $userId,
            'status' => ReviewStatus::Published,
        ]);

        return $review->load($this->relations());
    }

    public function update(Review $review, UpdateReviewData $data): Review
    {
        $review->forceFill($data->toArray())->save();

        return $review->refresh()->load($this->relations());
    }

    public function delete(Review $review): void
    {
        $review->delete();
    }

    public function refreshWorkshopRating(Workshop $workshop): Workshop
    {
        $stats = $workshop->reviews()
            ->published()
            ->selectRaw('COUNT(*) as reviews_count, COALESCE(AVG(rating), 0) as rating_avg')
            ->first();

        $workshop->forceFill([
            'reviews_count' => (int) $stats->reviews_count,
            'rating_avg' => round((float) $stats->rating_avg, 2),
        ])->save();

        return $workshop->refresh();
    }

    /**
     * @return array<int, string>
     */
    private function relations(): array
    {
        return [
            'user:id,name,phone,avatar,city,area,role,status,phone_verified_at,last_login_at,created_at',
            'workshop',
            'booking',
            'sosRequest',
        ];
    }
}
