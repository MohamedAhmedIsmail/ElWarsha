<?php

namespace App\Http\Requests\Workshop;

use App\DTOs\Workshop\WorkingHoursData;
use App\Enums\DayOfWeek;
use Illuminate\Validation\Rule;

class SyncWorkshopWorkingHoursRequest extends WorkshopOwnerRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'hours' => ['required', 'array', 'min:1', 'max:7'],
            'hours.*.day_of_week' => ['required', Rule::in(DayOfWeek::values())],
            'hours.*.opens_at' => ['nullable', 'date_format:H:i'],
            'hours.*.closes_at' => ['nullable', 'date_format:H:i'],
            'hours.*.is_closed' => ['required', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            foreach ($this->input('hours', []) as $index => $hour) {
                if (! (bool) ($hour['is_closed'] ?? false) && (empty($hour['opens_at']) || empty($hour['closes_at']))) {
                    $validator->errors()->add("hours.{$index}.opens_at", 'Opening and closing times are required when the day is open.');
                }
            }
        });
    }

    public function toDto(): WorkingHoursData
    {
        return new WorkingHoursData($this->validated('hours'));
    }
}
