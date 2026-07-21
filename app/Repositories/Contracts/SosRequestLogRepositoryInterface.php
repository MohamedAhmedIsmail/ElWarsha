<?php

namespace App\Repositories\Contracts;

use App\Enums\SosRequestStatus;
use App\Models\SosRequest;
use App\Models\SosRequestLog;

interface SosRequestLogRepositoryInterface
{
    public function create(SosRequest $sosRequest, ?SosRequestStatus $oldStatus, SosRequestStatus $newStatus, ?int $changedBy, ?string $notes = null): SosRequestLog;
}
