<?php

namespace App\Repositories\Eloquent;

use App\DTOs\ServiceLedger\StoreServiceLedgerData;
use App\DTOs\ServiceLedger\UpdateServiceLedgerData;
use App\Enums\ServiceLedgerMediaType;
use App\Models\Booking;
use App\Models\ServiceLedger;
use App\Models\ServiceLedgerMedia;
use App\Models\SosRequest;
use App\Models\Vehicle;
use App\Repositories\Contracts\ServiceLedgerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ServiceLedgerRepository implements ServiceLedgerRepositoryInterface
{
    public function listForVehicle(Vehicle $vehicle): Collection
    {
        return $vehicle->serviceLedgers()
            ->with($this->relations())
            ->latest('service_date')
            ->latest('id')
            ->get();
    }

    public function findForUser(int $userId, int $ledgerId): ?ServiceLedger
    {
        return ServiceLedger::query()
            ->where('user_id', $userId)
            ->with($this->relations())
            ->whereKey($ledgerId)
            ->first();
    }

    public function createForVehicle(int $userId, Vehicle $vehicle, StoreServiceLedgerData $data, ?string $invoicePath): ServiceLedger
    {
        $ledger = ServiceLedger::query()->create([
            ...$data->toArray($invoicePath),
            'user_id' => $userId,
            'vehicle_id' => $vehicle->id,
        ]);

        return $ledger->load($this->relations());
    }

    public function update(ServiceLedger $ledger, UpdateServiceLedgerData $data, ?string $invoicePath): ServiceLedger
    {
        $ledger->forceFill($data->toArray($invoicePath))->save();

        return $ledger->refresh()->load($this->relations());
    }

    public function delete(ServiceLedger $ledger): void
    {
        $ledger->delete();
    }

    public function createFromCompletedBooking(Booking $booking): ServiceLedger
    {
        return ServiceLedger::query()->create([
            'user_id' => $booking->user_id,
            'vehicle_id' => $booking->vehicle_id,
            'workshop_id' => $booking->workshop_id,
            'booking_id' => $booking->id,
            'diagnosis_id' => $booking->diagnosis_id,
            'title' => $booking->service?->name ?? 'Workshop service',
            'description' => $booking->description,
            'service_date' => now()->toDateString(),
            'mileage_km' => $booking->vehicle?->mileage_km,
        ])->load($this->relations());
    }

    public function createFromCompletedSosRequest(SosRequest $sosRequest): ServiceLedger
    {
        return ServiceLedger::query()->create([
            'user_id' => $sosRequest->user_id,
            'vehicle_id' => $sosRequest->vehicle_id,
            'workshop_id' => $sosRequest->assignedProvider?->workshop_id,
            'sos_request_id' => $sosRequest->id,
            'title' => $sosRequest->serviceType->name,
            'description' => $sosRequest->description,
            'service_date' => now()->toDateString(),
            'mileage_km' => $sosRequest->vehicle?->mileage_km,
        ])->load($this->relations());
    }

    public function createMedia(ServiceLedger $ledger, ServiceLedgerMediaType $mediaType, string $path): ServiceLedgerMedia
    {
        return $ledger->media()->create([
            'media_type' => $mediaType,
            'file_path' => $path,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function relations(): array
    {
        return [
            'vehicle.brand',
            'vehicle.model',
            'workshop',
            'booking',
            'diagnosis.affectedCategory',
            'sosRequest.serviceType',
            'sosRequest.assignedProvider',
            'maintenanceItem.serviceCategory',
            'media',
        ];
    }
}
