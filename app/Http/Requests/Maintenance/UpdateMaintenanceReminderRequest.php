<?php

namespace App\Http\Requests\Maintenance;

use App\DTOs\Maintenance\UpdateMaintenanceReminderData;
use App\Enums\MaintenanceReminderStatus;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateMaintenanceReminderRequest extends FormRequest
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
            'last_done_at' => ['sometimes', 'nullable', 'date'],
            'last_done_mileage' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'next_due_at' => ['sometimes', 'nullable', 'date'],
            'next_due_mileage' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'reminder_before_days' => ['sometimes', 'integer', 'min:0', 'max:365'],
            'status' => ['required', new Enum(MaintenanceReminderStatus::class)],
            'notes' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }

    public function toDto(): UpdateMaintenanceReminderData
    {
        return new UpdateMaintenanceReminderData(
            lastDoneAt: $this->dateOrNull('last_done_at'),
            lastDoneMileage: $this->validated('last_done_mileage') === null ? null : (int) $this->validated('last_done_mileage'),
            nextDueAt: $this->dateOrNull('next_due_at'),
            nextDueMileage: $this->validated('next_due_mileage') === null ? null : (int) $this->validated('next_due_mileage'),
            reminderBeforeDays: (int) ($this->validated('reminder_before_days') ?? 7),
            status: MaintenanceReminderStatus::from($this->validated('status')),
            notes: $this->validated('notes'),
        );
    }

    private function dateOrNull(string $key): ?CarbonImmutable
    {
        $value = $this->validated($key);

        return $value === null ? null : CarbonImmutable::parse($value);
    }
}
