<?php

namespace App\Repositories\Eloquent;

use App\Enums\SosRequestStatus;
use App\Models\SosRequest;
use App\Models\SosRequestLog;
use App\Repositories\Contracts\SosRequestLogRepositoryInterface;

class SosRequestLogRepository implements SosRequestLogRepositoryInterface
{
    public function create(SosRequest $sosRequest, ?SosRequestStatus $oldStatus, SosRequestStatus $newStatus, ?int $changedBy, ?string $notes = null): SosRequestLog
    {
        return $sosRequest->logs()->create([
            'old_status' => $oldStatus?->value,
            'new_status' => $newStatus->value,
            'changed_by' => $changedBy,
            'notes' => $notes,
        ]);
    }
}
