<?php

namespace App\Services;

use App\DTOs\Review\StoreReviewData;
use App\DTOs\Review\UpdateReviewData;
use App\Models\Review;
use App\Models\User;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Repositories\Contracts\SosRequestRepositoryInterface;
use App\Repositories\Contracts\WorkshopRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReviewService
{
    public function __construct(
        private readonly ReviewRepositoryInterface $reviews,
        private readonly WorkshopRepositoryInterface $workshops,
        private readonly BookingRepositoryInterface $bookings,
        private readonly SosRequestRepositoryInterface $sosRequests,
    ) {
    }

    /**
     * @return Collection<int, Review>
     */
    public function listForUser(User $user): Collection
    {
        return $this->reviews->listForUser($user->id);
    }

    public function create(User $user, StoreReviewData $data): Review
    {
        $workshop = $this->workshops->findApproved($data->workshopId)
            ?? throw ValidationException::withMessages(['workshop_id' => __('The selected workshop was not found.')]);

        $this->validateBooking($user, $data);
        $this->validateSosRequest($user, $data);

        return DB::transaction(function () use ($user, $data, $workshop): Review {
            $review = $this->reviews->createForUser($user->id, $data);
            $this->reviews->refreshWorkshopRating($workshop);

            return $review->refresh()->load(['user', 'workshop', 'booking', 'sosRequest']);
        });
    }

    public function update(User $user, int $reviewId, UpdateReviewData $data): Review
    {
        $review = $this->getForUser($user, $reviewId);
        $workshop = $review->workshop;

        return DB::transaction(function () use ($review, $data, $workshop): Review {
            $review = $this->reviews->update($review, $data);
            $this->reviews->refreshWorkshopRating($workshop);

            return $review;
        });
    }

    public function delete(User $user, int $reviewId): void
    {
        $review = $this->getForUser($user, $reviewId);
        $workshop = $review->workshop;

        DB::transaction(function () use ($review, $workshop): void {
            $this->reviews->delete($review);
            $this->reviews->refreshWorkshopRating($workshop);
        });
    }

    private function getForUser(User $user, int $reviewId): Review
    {
        return $this->reviews->findForUser($user->id, $reviewId)
            ?? throw new NotFoundHttpException('Review not found.');
    }

    private function validateBooking(User $user, StoreReviewData $data): void
    {
        if ($data->bookingId === null) {
            return;
        }

        $booking = $this->bookings->findForUser($user->id, $data->bookingId);

        if (! $booking) {
            throw ValidationException::withMessages(['booking_id' => __('The selected booking was not found.')]);
        }

        if ((int) $booking->workshop_id !== $data->workshopId) {
            throw ValidationException::withMessages(['booking_id' => __('The selected booking does not belong to this workshop.')]);
        }

        if ($this->reviews->existsForBooking($data->bookingId)) {
            throw ValidationException::withMessages(['booking_id' => __('A review already exists for this booking.')]);
        }
    }

    private function validateSosRequest(User $user, StoreReviewData $data): void
    {
        if ($data->sosRequestId === null) {
            return;
        }

        $sosRequest = $this->sosRequests->findForUser($user->id, $data->sosRequestId);

        if (! $sosRequest) {
            throw ValidationException::withMessages(['sos_request_id' => __('The selected SOS request was not found.')]);
        }

        $providerWorkshopId = $sosRequest->assignedProvider?->workshop_id;

        if ($providerWorkshopId !== null && (int) $providerWorkshopId !== $data->workshopId) {
            throw ValidationException::withMessages(['sos_request_id' => __('The selected SOS request does not belong to this workshop.')]);
        }
    }
}
