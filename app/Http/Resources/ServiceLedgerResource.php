<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceLedgerResource extends JsonResource
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
            'workshop' => new WorkshopResource($this->whenLoaded('workshop')),
            'workshop_id' => $this->workshop_id,
            'booking' => new BookingResource($this->whenLoaded('booking')),
            'booking_id' => $this->booking_id,
            'diagnosis' => new DiagnosisResource($this->whenLoaded('diagnosis')),
            'diagnosis_id' => $this->diagnosis_id,
            'sos_request' => new SosRequestResource($this->whenLoaded('sosRequest')),
            'sos_request_id' => $this->sos_request_id,
            'maintenance_item' => new MaintenanceItemResource($this->whenLoaded('maintenanceItem')),
            'maintenance_item_id' => $this->maintenance_item_id,
            'title' => $this->title,
            'description' => $this->description,
            'service_date' => $this->service_date?->toDateString(),
            'cost' => $this->cost,
            'mileage_km' => $this->mileage_km,
            'invoice_file' => $this->invoice_file,
            'media' => ServiceLedgerMediaResource::collection($this->whenLoaded('media')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
