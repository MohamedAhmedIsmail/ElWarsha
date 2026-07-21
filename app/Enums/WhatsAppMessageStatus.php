<?php

namespace App\Enums;

enum WhatsAppMessageStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Failed = 'failed';
    case Delivered = 'delivered';
    case Read = 'read';
}
