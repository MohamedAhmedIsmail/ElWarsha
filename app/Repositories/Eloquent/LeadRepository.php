<?php

namespace App\Repositories\Eloquent;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Models\Booking;
use App\Models\Lead;
use App\Repositories\Contracts\LeadRepositoryInterface;

class LeadRepository implements LeadRepositoryInterface
{
    public function createFromBooking(Booking $booking): Lead
    {
        return Lead::query()->create([
            'workshop_id' => $booking->workshop_id,
            'user_id' => $booking->user_id,
            'vehicle_id' => $booking->vehicle_id,
            'booking_id' => $booking->id,
            'diagnosis_id' => $booking->diagnosis_id,
            'source' => LeadSource::Booking,
            'status' => LeadStatus::New,
        ]);
    }
}
