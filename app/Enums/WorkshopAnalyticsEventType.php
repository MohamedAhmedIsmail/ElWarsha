<?php

namespace App\Enums;

enum WorkshopAnalyticsEventType: string
{
    case ProfileView = 'profile_view';
    case CallClick = 'call_click';
    case WhatsappClick = 'whatsapp_click';
    case DirectionsClick = 'directions_click';
    case BookingClick = 'booking_click';
    case SosClick = 'sos_click';
}
