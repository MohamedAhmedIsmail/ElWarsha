<?php

namespace App\Http\Resources;

use App\Enums\MaintenanceReminderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleMaintenanceReminderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'vehicle_id' => $this->vehicle_id,
            'maintenance_item' => new MaintenanceItemResource($this->whenLoaded('maintenanceItem')),
            'maintenance_item_id' => $this->maintenance_item_id,
            'last_done_at' => $this->last_done_at?->toDateString(),
            'last_done_mileage' => $this->last_done_mileage,
            'next_due_at' => $this->next_due_at?->toDateString(),
            'next_due_mileage' => $this->next_due_mileage,
            'reminder_before_days' => $this->reminder_before_days,
            'status' => $this->status instanceof MaintenanceReminderStatus ? $this->status->value : $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
