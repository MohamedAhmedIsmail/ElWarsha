<?php

namespace App\Repositories\Eloquent;

use App\Enums\WhatsAppMessageStatus;
use App\Models\Booking;
use App\Models\WhatsappMessage;
use App\Repositories\Contracts\WhatsappMessageRepositoryInterface;

class WhatsappMessageRepository implements WhatsappMessageRepositoryInterface
{
    public function createWorkshopBookingNotification(Booking $booking): WhatsappMessage
    {
        return WhatsappMessage::query()->create([
            'user_id' => $booking->user_id,
            'workshop_id' => $booking->workshop_id,
            'phone' => $booking->workshop->whatsapp ?: $booking->workshop->phone,
            'message' => 'New booking request #' . $booking->id . ' from ' . $booking->user->name . '.',
            'template_name' => 'workshop_booking_request',
            'status' => WhatsAppMessageStatus::Pending,
        ]);
    }
}
