<?php

namespace App\Repositories\Contracts;

use App\Models\Booking;
use App\Models\Lead;

interface LeadRepositoryInterface
{
    public function createFromBooking(Booking $booking): Lead;
}
