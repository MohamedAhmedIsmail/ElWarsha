<?php

namespace App\DTOs\Review;

class StoreReviewData
{
    public function __construct(
        public readonly int $workshopId,
        public readonly ?int $bookingId,
        public readonly ?int $sosRequestId,
        public readonly int $rating,
        public readonly ?int $qualityRating,
        public readonly ?int $priceRating,
        public readonly ?int $punctualityRating,
        public readonly ?int $behaviorRating,
        public readonly ?string $comment,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'workshop_id' => $this->workshopId,
            'booking_id' => $this->bookingId,
            'sos_request_id' => $this->sosRequestId,
            'rating' => $this->rating,
            'quality_rating' => $this->qualityRating,
            'price_rating' => $this->priceRating,
            'punctuality_rating' => $this->punctualityRating,
            'behavior_rating' => $this->behaviorRating,
            'comment' => $this->comment,
        ];
    }
}
