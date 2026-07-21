<?php

namespace App\Repositories\Contracts;

use App\DTOs\ServiceLedger\StoreServiceLedgerData;
use App\DTOs\ServiceLedger\UpdateServiceLedgerData;
use App\Enums\ServiceLedgerMediaType;
use App\Models\Booking;
use App\Models\ServiceLedger;
use App\Models\ServiceLedgerMedia;
use App\Models\SosRequest;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;

interface ServiceLedgerRepositoryInterface
{
    /**
     * @return Collection<int, ServiceLedger>
     */
    public function listForVehicle(Vehicle $vehicle): Collection;

    public function findForUser(int $userId, int $ledgerId): ?ServiceLedger;

    public function createForVehicle(int $userId, Vehicle $vehicle, StoreServiceLedgerData $data, ?string $invoicePath): ServiceLedger;

    public function update(ServiceLedger $ledger, UpdateServiceLedgerData $data, ?string $invoicePath): ServiceLedger;

    public function delete(ServiceLedger $ledger): void;

    public function createFromCompletedBooking(Booking $booking): ServiceLedger;

    public function createFromCompletedSosRequest(SosRequest $sosRequest): ServiceLedger;

    public function createMedia(ServiceLedger $ledger, ServiceLedgerMediaType $mediaType, string $path): ServiceLedgerMedia;
}
