<?php

namespace App\Repositories\Contracts;

use App\Models\Booking;
use App\Models\ServiceLedger;

interface ServiceLedgerRepositoryInterface
{
    public function createFromCompletedBooking(Booking $booking): ServiceLedger;
}
