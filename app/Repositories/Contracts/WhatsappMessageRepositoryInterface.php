<?php

namespace App\Repositories\Contracts;

use App\Models\Booking;
use App\Models\WhatsappMessage;

interface WhatsappMessageRepositoryInterface
{
    public function createWorkshopBookingNotification(Booking $booking): WhatsappMessage;
}
