<?php

namespace App\DTOs\Crm;

use App\Enums\LeadStatus;

class UpdateLeadStatusData
{
    public function __construct(public readonly LeadStatus $status)
    {
    }
}
