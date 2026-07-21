<?php

namespace App\Enums;

enum LeadSource: string
{
    case ProfileView = 'profile_view';
    case CallClick = 'call_click';
    case WhatsappClick = 'whatsapp_click';
    case DirectionsClick = 'directions_click';
    case Booking = 'booking';
    case Sos = 'sos';
    case DiagnosisRecommendation = 'diagnosis_recommendation';
}
