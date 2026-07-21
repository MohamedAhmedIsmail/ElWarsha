<?php

namespace App\Services;

use App\DTOs\ServiceLedger\StoreServiceLedgerData;
use App\DTOs\ServiceLedger\UpdateServiceLedgerData;
use App\DTOs\ServiceLedger\UploadServiceLedgerMediaData;
use App\Models\ServiceLedger;
use App\Models\ServiceLedgerMedia;
use App\Models\User;
use App\Models\Vehicle;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\DiagnosisRepositoryInterface;
use App\Repositories\Contracts\MaintenanceItemRepositoryInterface;
use App\Repositories\Contracts\ServiceLedgerRepositoryInterface;
use App\Repositories\Contracts\SosRequestRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Repositories\Contracts\WorkshopRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServiceLedgerService
{
    public function __construct(
        private readonly ServiceLedgerRepositoryInterface $ledgers,
        private readonly VehicleRepositoryInterface $vehicles,
        private readonly WorkshopRepositoryInterface $workshops,
        private readonly BookingRepositoryInterface $bookings,
        private readonly DiagnosisRepositoryInterface $diagnoses,
        private readonly SosRequestRepositoryInterface $sosRequests,
        private readonly MaintenanceItemRepositoryInterface $maintenanceItems,
    ) {
    }

    /**
     * @return Collection<int, ServiceLedger>
     */
    public function listForVehicle(User $user, int $vehicleId): Collection
    {
        return $this->ledgers->listForVehicle($this->getVehicleForUser($user, $vehicleId));
    }

    public function getForUser(User $user, int $ledgerId): ServiceLedger
    {
        return $this->ledgers->findForUser($user->id, $ledgerId)
            ?? throw new NotFoundHttpException('Service ledger entry not found.');
    }

    public function create(User $user, int $vehicleId, StoreServiceLedgerData $data): ServiceLedger
    {
        $vehicle = $this->getVehicleForUser($user, $vehicleId);
        $this->validateLinks($user, $vehicle, $data);
        $invoicePath = $data->invoiceFile?->store("service-ledgers/{$vehicle->id}/invoices", 'public');

        return DB::transaction(fn (): ServiceLedger => $this->ledgers->createForVehicle($user->id, $vehicle, $data, $invoicePath));
    }

    public function update(User $user, int $ledgerId, UpdateServiceLedgerData $data): ServiceLedger
    {
        $ledger = $this->getForUser($user, $ledgerId);
        $vehicle = $ledger->vehicle;
        $this->validateLinks($user, $vehicle, $data);
        $invoicePath = $data->invoiceFile?->store("service-ledgers/{$ledger->id}/invoices", 'public');

        return DB::transaction(fn (): ServiceLedger => $this->ledgers->update($ledger, $data, $invoicePath));
    }

    public function delete(User $user, int $ledgerId): void
    {
        $ledger = $this->getForUser($user, $ledgerId);

        DB::transaction(fn () => $this->ledgers->delete($ledger));
    }

    /**
     * @return Collection<int, ServiceLedgerMedia>
     */
    public function uploadMedia(User $user, int $ledgerId, UploadServiceLedgerMediaData $data): Collection
    {
        $ledger = $this->getForUser($user, $ledgerId);

        return DB::transaction(function () use ($ledger, $data): Collection {
            $created = new Collection();

            foreach ($data->files as $file) {
                $path = $file->store("service-ledgers/{$ledger->id}/media", 'public');
                $created->push($this->ledgers->createMedia($ledger, $data->mediaType, $path));
            }

            return $created;
        });
    }

    private function getVehicleForUser(User $user, int $vehicleId): Vehicle
    {
        return $this->vehicles->findForUser($user->id, $vehicleId)
            ?? throw new NotFoundHttpException('Vehicle not found.');
    }

    private function validateLinks(User $user, Vehicle $vehicle, StoreServiceLedgerData|UpdateServiceLedgerData $data): void
    {
        if ($data->workshopId !== null && ! $this->workshops->findApproved($data->workshopId)) {
            throw ValidationException::withMessages(['workshop_id' => __('The selected workshop was not found.')]);
        }

        if ($data->maintenanceItemId !== null && ! $this->maintenanceItems->findActive($data->maintenanceItemId)) {
            throw ValidationException::withMessages(['maintenance_item_id' => __('The selected maintenance item was not found.')]);
        }

        if ($data->bookingId !== null) {
            $booking = $this->bookings->findForUser($user->id, $data->bookingId);

            if (! $booking || (int) $booking->vehicle_id !== (int) $vehicle->id) {
                throw ValidationException::withMessages(['booking_id' => __('The selected booking was not found for this vehicle.')]);
            }
        }

        if ($data->diagnosisId !== null) {
            $diagnosis = $this->diagnoses->findForUser($user->id, $data->diagnosisId);

            if (! $diagnosis || (int) $diagnosis->vehicle_id !== (int) $vehicle->id) {
                throw ValidationException::withMessages(['diagnosis_id' => __('The selected diagnosis was not found for this vehicle.')]);
            }
        }

        if ($data->sosRequestId !== null) {
            $sosRequest = $this->sosRequests->findForUser($user->id, $data->sosRequestId);

            if (! $sosRequest || (int) $sosRequest->vehicle_id !== (int) $vehicle->id) {
                throw ValidationException::withMessages(['sos_request_id' => __('The selected SOS request was not found for this vehicle.')]);
            }
        }
    }
}
