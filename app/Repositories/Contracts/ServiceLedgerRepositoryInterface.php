<?php

namespace App\Repositories\Contracts;

use App\Models\Booking;
use App\Models\ServiceLedger;
use App\Models\SosRequest;

interface ServiceLedgerRepositoryInterface
{
    public function createFromCompletedBooking(Booking $booking): ServiceLedger;

    public function createFromCompletedSosRequest(SosRequest $sosRequest): ServiceLedger;
}
