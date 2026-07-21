<?php

namespace App\Http\Requests\Review;

use App\DTOs\Review\StoreReviewData;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'workshop_id' => ['required', 'integer', 'exists:workshops,id'],
            'booking_id' => ['sometimes', 'nullable', 'integer', 'exists:bookings,id'],
            'sos_request_id' => ['sometimes', 'nullable', 'integer', 'exists:sos_requests,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'quality_rating' => ['sometimes', 'nullable', 'integer', 'between:1,5'],
            'price_rating' => ['sometimes', 'nullable', 'integer', 'between:1,5'],
            'punctuality_rating' => ['sometimes', 'nullable', 'integer', 'between:1,5'],
            'behavior_rating' => ['sometimes', 'nullable', 'integer', 'between:1,5'],
            'comment' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }

    public function toDto(): StoreReviewData
    {
        return new StoreReviewData(
            workshopId: (int) $this->validated('workshop_id'),
            bookingId: $this->validated('booking_id') === null ? null : (int) $this->validated('booking_id'),
            sosRequestId: $this->validated('sos_request_id') === null ? null : (int) $this->validated('sos_request_id'),
            rating: (int) $this->validated('rating'),
            qualityRating: $this->validated('quality_rating') === null ? null : (int) $this->validated('quality_rating'),
            priceRating: $this->validated('price_rating') === null ? null : (int) $this->validated('price_rating'),
            punctualityRating: $this->validated('punctuality_rating') === null ? null : (int) $this->validated('punctuality_rating'),
            behaviorRating: $this->validated('behavior_rating') === null ? null : (int) $this->validated('behavior_rating'),
            comment: $this->validated('comment'),
        );
    }
}
