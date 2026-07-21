<?php

namespace App\Repositories\Eloquent;

use App\Models\Booking;
use App\Models\ServiceLedger;
use App\Repositories\Contracts\ServiceLedgerRepositoryInterface;

class ServiceLedgerRepository implements ServiceLedgerRepositoryInterface
{
    public function createFromCompletedBooking(Booking $booking): ServiceLedger
    {
        return ServiceLedger::query()->create([
            'user_id' => $booking->user_id,
            'vehicle_id' => $booking->vehicle_id,
            'workshop_id' => $booking->workshop_id,
            'booking_id' => $booking->id,
            'diagnosis_id' => $booking->diagnosis_id,
            'title' => $booking->service?->name ?? 'Workshop service',
            'description' => $booking->description,
            'service_date' => now()->toDateString(),
            'mileage_km' => $booking->vehicle?->mileage_km,
        ]);
    }
}
