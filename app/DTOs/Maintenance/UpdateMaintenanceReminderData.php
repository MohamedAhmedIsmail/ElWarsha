<?php

namespace App\DTOs\Maintenance;

use App\Enums\MaintenanceReminderStatus;
use Carbon\CarbonImmutable;

class UpdateMaintenanceReminderData
{
    public function __construct(
        public readonly ?CarbonImmutable $lastDoneAt,
        public readonly ?int $lastDoneMileage,
        public readonly ?CarbonImmutable $nextDueAt,
        public readonly ?int $nextDueMileage,
        public readonly int $reminderBeforeDays,
        public readonly MaintenanceReminderStatus $status,
        public readonly ?string $notes,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'last_done_at' => $this->lastDoneAt?->toDateString(),
            'last_done_mileage' => $this->lastDoneMileage,
            'next_due_at' => $this->nextDueAt?->toDateString(),
            'next_due_mileage' => $this->nextDueMileage,
            'reminder_before_days' => $this->reminderBeforeDays,
            'status' => $this->status,
            'notes' => $this->notes,
        ];
    }
}
