<?php

namespace App\Http\Requests\ServiceLedger;

use App\DTOs\ServiceLedger\StoreServiceLedgerData;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceLedgerRequest extends FormRequest
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
            'workshop_id' => ['sometimes', 'nullable', 'integer', 'exists:workshops,id'],
            'booking_id' => ['sometimes', 'nullable', 'integer', 'exists:bookings,id'],
            'diagnosis_id' => ['sometimes', 'nullable', 'integer', 'exists:diagnoses,id'],
            'sos_request_id' => ['sometimes', 'nullable', 'integer', 'exists:sos_requests,id'],
            'maintenance_item_id' => ['sometimes', 'nullable', 'integer', 'exists:maintenance_items,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'service_date' => ['required', 'date'],
            'cost' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'mileage_km' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'invoice_file' => ['sometimes', 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function toDto(): StoreServiceLedgerData
    {
        return new StoreServiceLedgerData(
            workshopId: $this->intOrNull('workshop_id'),
            bookingId: $this->intOrNull('booking_id'),
            diagnosisId: $this->intOrNull('diagnosis_id'),
            sosRequestId: $this->intOrNull('sos_request_id'),
            maintenanceItemId: $this->intOrNull('maintenance_item_id'),
            title: $this->validated('title'),
            description: $this->validated('description'),
            serviceDate: CarbonImmutable::parse($this->validated('service_date')),
            cost: $this->validated('cost') === null ? null : (float) $this->validated('cost'),
            mileageKm: $this->intOrNull('mileage_km'),
            invoiceFile: $this->file('invoice_file'),
        );
    }

    private function intOrNull(string $key): ?int
    {
        return $this->validated($key) === null ? null : (int) $this->validated($key);
    }
}
