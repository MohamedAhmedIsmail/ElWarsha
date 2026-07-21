<?php

namespace App\DTOs\ServiceLedger;

use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;

class StoreServiceLedgerData
{
    public function __construct(
        public readonly ?int $workshopId,
        public readonly ?int $bookingId,
        public readonly ?int $diagnosisId,
        public readonly ?int $sosRequestId,
        public readonly ?int $maintenanceItemId,
        public readonly string $title,
        public readonly ?string $description,
        public readonly CarbonImmutable $serviceDate,
        public readonly ?float $cost,
        public readonly ?int $mileageKm,
        public readonly ?UploadedFile $invoiceFile,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(?string $invoicePath = null): array
    {
        return [
            'workshop_id' => $this->workshopId,
            'booking_id' => $this->bookingId,
            'diagnosis_id' => $this->diagnosisId,
            'sos_request_id' => $this->sosRequestId,
            'maintenance_item_id' => $this->maintenanceItemId,
            'title' => $this->title,
            'description' => $this->description,
            'service_date' => $this->serviceDate->toDateString(),
            'cost' => $this->cost,
            'mileage_km' => $this->mileageKm,
            'invoice_file' => $invoicePath,
        ];
    }
}
