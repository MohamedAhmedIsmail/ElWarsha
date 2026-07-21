<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Booking\StoreBookingData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Workshop;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BookingRepository implements BookingRepositoryInterface
{
    public function listForUser(int $userId): Collection
    {
        return Booking::query()
            ->ownedBy($userId)
            ->with($this->relations())
            ->latest('id')
            ->get();
    }

    public function findForUser(int $userId, int $bookingId): ?Booking
    {
        return Booking::query()
            ->ownedBy($userId)
            ->with($this->relations())
            ->whereKey($bookingId)
            ->first();
    }

    public function listForWorkshop(Workshop $workshop): Collection
    {
        return $workshop->bookings()
            ->with($this->relations())
            ->latest('id')
            ->get();
    }

    public function findForWorkshop(Workshop $workshop, int $bookingId): ?Booking
    {
        return $workshop->bookings()
            ->with($this->relations())
            ->whereKey($bookingId)
            ->first();
    }

    public function createForUser(int $userId, StoreBookingData $data): Booking
    {
        $booking = Booking::query()->create([
            'user_id' => $userId,
            'vehicle_id' => $data->vehicleId,
            'workshop_id' => $data->workshopId,
            'diagnosis_id' => $data->diagnosisId,
            'service_id' => $data->serviceId,
            'scheduled_at' => $data->scheduledAt,
            'description' => $data->description,
            'status' => BookingStatus::Pending,
        ]);

        return $booking->load($this->relations());
    }

    public function updateStatus(Booking $booking, BookingStatus $status): Booking
    {
        $attributes = ['status' => $status];

        if ($status === BookingStatus::Completed) {
            $attributes['completed_at'] = now();
        }

        if ($status === BookingStatus::Cancelled) {
            $attributes['cancelled_at'] = now();
        }

        $booking->forceFill($attributes)->save();

        return $booking->refresh()->load($this->relations());
    }

    /**
     * @return array<int, string>
     */
    private function relations(): array
    {
        return [
            'vehicle.brand',
            'vehicle.model',
            'user',
            'workshop',
            'workshop.owner',
            'diagnosis.affectedCategory',
            'service.category',
            'statusLogs',
        ];
    }
}
