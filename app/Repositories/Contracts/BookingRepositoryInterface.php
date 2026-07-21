<?php

namespace App\Repositories\Contracts;

use App\DTOs\Booking\StoreBookingData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Collection;

interface BookingRepositoryInterface
{
    /**
     * @return Collection<int, Booking>
     */
    public function listForUser(int $userId): Collection;

    public function findForUser(int $userId, int $bookingId): ?Booking;

    /**
     * @return Collection<int, Booking>
     */
    public function listForWorkshop(Workshop $workshop): Collection;

    public function findForWorkshop(Workshop $workshop, int $bookingId): ?Booking;

    public function createForUser(int $userId, StoreBookingData $data): Booking;

    public function updateStatus(Booking $booking, BookingStatus $status): Booking;
}
