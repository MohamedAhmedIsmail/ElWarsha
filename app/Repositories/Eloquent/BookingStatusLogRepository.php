<?php

namespace App\Repositories\Eloquent;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingStatusLog;
use App\Repositories\Contracts\BookingStatusLogRepositoryInterface;

class BookingStatusLogRepository implements BookingStatusLogRepositoryInterface
{
    public function create(Booking $booking, ?BookingStatus $oldStatus, BookingStatus $newStatus, ?int $changedBy, ?string $notes = null): BookingStatusLog
    {
        return $booking->statusLogs()->create([
            'old_status' => $oldStatus?->value,
            'new_status' => $newStatus->value,
            'changed_by' => $changedBy,
            'notes' => $notes,
        ]);
    }
}
