<?php

namespace App\Repositories\Contracts;

use App\Models\Booking;
use App\Models\Lead;
use App\Models\SosRequest;

interface LeadRepositoryInterface
{
    public function createFromBooking(Booking $booking): Lead;

    public function createFromSosRequest(SosRequest $sosRequest): Lead;
}
