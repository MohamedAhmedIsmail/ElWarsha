<?php

namespace App\Http\Requests\Booking;

use App\DTOs\Booking\BookingStatusChangeData;
use Illuminate\Foundation\Http\FormRequest;

class BookingStatusChangeRequest extends FormRequest
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
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }

    public function toDto(): BookingStatusChangeData
    {
        return new BookingStatusChangeData($this->validated('notes'));
    }
}
