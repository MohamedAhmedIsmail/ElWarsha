<?php

namespace App\Enums;

enum LeadStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Booked = 'booked';
    case InService = 'in_service';
    case Completed = 'completed';
    case Lost = 'lost';
    case FollowUpNeeded = 'follow_up_needed';
}
