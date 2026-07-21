<?php

namespace App\Http\Requests\Review;

use App\DTOs\Review\UpdateReviewData;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
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
            'rating' => ['required', 'integer', 'between:1,5'],
            'quality_rating' => ['sometimes', 'nullable', 'integer', 'between:1,5'],
            'price_rating' => ['sometimes', 'nullable', 'integer', 'between:1,5'],
            'punctuality_rating' => ['sometimes', 'nullable', 'integer', 'between:1,5'],
            'behavior_rating' => ['sometimes', 'nullable', 'integer', 'between:1,5'],
            'comment' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }

    public function toDto(): UpdateReviewData
    {
        return new UpdateReviewData(
            rating: (int) $this->validated('rating'),
            qualityRating: $this->validated('quality_rating') === null ? null : (int) $this->validated('quality_rating'),
            priceRating: $this->validated('price_rating') === null ? null : (int) $this->validated('price_rating'),
            punctualityRating: $this->validated('punctuality_rating') === null ? null : (int) $this->validated('punctuality_rating'),
            behaviorRating: $this->validated('behavior_rating') === null ? null : (int) $this->validated('behavior_rating'),
            comment: $this->validated('comment'),
        );
    }
}
