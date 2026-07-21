<?php

namespace App\Enums;

enum MaintenanceReminderStatus: string
{
    case Active = 'active';
    case Done = 'done';
    case Skipped = 'skipped';
    case Cancelled = 'cancelled';
}
