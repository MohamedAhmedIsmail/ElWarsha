<?php

namespace App\DTOs\Crm;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;

class LeadFilterData
{
    public function __construct(
        public readonly ?LeadSource $source,
        public readonly ?LeadStatus $status,
    ) {
    }
}
