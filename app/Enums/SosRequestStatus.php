<?php

namespace App\Enums;

enum SosRequestStatus: string
{
    case Pending = 'pending';
    case Assigned = 'assigned';
    case Accepted = 'accepted';
    case OnTheWay = 'on_the_way';
    case Arrived = 'arrived';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
