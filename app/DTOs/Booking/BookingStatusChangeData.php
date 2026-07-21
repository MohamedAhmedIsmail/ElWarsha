<?php

namespace App\DTOs\Booking;

final readonly class BookingStatusChangeData
{
    public function __construct(public ?string $notes)
    {
    }
}
