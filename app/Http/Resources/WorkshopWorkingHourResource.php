<?php

namespace App\Http\Resources;

use App\Enums\DayOfWeek;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkshopWorkingHourResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'day_of_week' => $this->day_of_week instanceof DayOfWeek ? $this->day_of_week->value : $this->day_of_week,
            'opens_at' => $this->opens_at,
            'closes_at' => $this->closes_at,
            'is_closed' => $this->is_closed,
        ];
    }
}
