<?php

namespace App\Repositories\Contracts;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingStatusLog;

interface BookingStatusLogRepositoryInterface
{
    public function create(Booking $booking, ?BookingStatus $oldStatus, BookingStatus $newStatus, ?int $changedBy, ?string $notes = null): BookingStatusLog;
}
