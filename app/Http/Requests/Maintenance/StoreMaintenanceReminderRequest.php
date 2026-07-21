<?php

namespace App\Http\Requests\Maintenance;

use App\DTOs\Maintenance\StoreMaintenanceReminderData;
use App\Enums\MaintenanceReminderStatus;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreMaintenanceReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'maintenance_item_id' => ['required', 'integer', 'exists:maintenance_items,id'],
            'last_done_at' => ['sometimes', 'nullable', 'date'],
            'last_done_mileage' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'next_due_at' => ['sometimes', 'nullable', 'date'],
            'next_due_mileage' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'reminder_before_days' => ['sometimes', 'integer', 'min:0', 'max:365'],
            'status' => ['sometimes', new Enum(MaintenanceReminderStatus::class)],
            'notes' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }

    public function toDto(): StoreMaintenanceReminderData
    {
        return new StoreMaintenanceReminderData(
            maintenanceItemId: (int) $this->validated('maintenance_item_id'),
            lastDoneAt: $this->dateOrNull('last_done_at'),
            lastDoneMileage: $this->validated('last_done_mileage') === null ? null : (int) $this->validated('last_done_mileage'),
            nextDueAt: $this->dateOrNull('next_due_at'),
            nextDueMileage: $this->validated('next_due_mileage') === null ? null : (int) $this->validated('next_due_mileage'),
            reminderBeforeDays: (int) ($this->validated('reminder_before_days') ?? 7),
            status: MaintenanceReminderStatus::from($this->validated('status') ?? MaintenanceReminderStatus::Active->value),
            notes: $this->validated('notes'),
        );
    }

    private function dateOrNull(string $key): ?CarbonImmutable
    {
        $value = $this->validated($key);

        return $value === null ? null : CarbonImmutable::parse($value);
    }
}
