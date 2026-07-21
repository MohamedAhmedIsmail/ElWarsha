<?php

namespace App\Services;

use App\DTOs\Sos\SosStatusChangeData;
use App\DTOs\Sos\StoreSosRequestData;
use App\Enums\SosRequestStatus;
use App\Enums\UserRole;
use App\Models\SosRequest;
use App\Models\User;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\Contracts\ServiceLedgerRepositoryInterface;
use App\Repositories\Contracts\SosProviderRepositoryInterface;
use App\Repositories\Contracts\SosRequestLogRepositoryInterface;
use App\Repositories\Contracts\SosRequestRepositoryInterface;
use App\Repositories\Contracts\SosServiceTypeRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Repositories\Contracts\WhatsappMessageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SosService
{
    /**
     * @var array<string, array<int, string>>
     */
    private array $allowedTransitions = [
        SosRequestStatus::Assigned->value => [SosRequestStatus::Accepted->value, SosRequestStatus::Pending->value, SosRequestStatus::Cancelled->value],
        SosRequestStatus::Accepted->value => [SosRequestStatus::OnTheWay->value],
        SosRequestStatus::OnTheWay->value => [SosRequestStatus::Arrived->value],
        SosRequestStatus::Arrived->value => [SosRequestStatus::Completed->value],
        SosRequestStatus::Pending->value => [SosRequestStatus::Cancelled->value],
    ];

    public function __construct(
        private readonly SosServiceTypeRepositoryInterface $serviceTypes,
        private readonly SosProviderRepositoryInterface $providers,
        private readonly SosRequestRepositoryInterface $sosRequests,
        private readonly SosRequestLogRepositoryInterface $logs,
        private readonly VehicleRepositoryInterface $vehicles,
        private readonly NotificationRepositoryInterface $notifications,
        private readonly WhatsappMessageRepositoryInterface $whatsappMessages,
        private readonly LeadRepositoryInterface $leads,
        private readonly ServiceLedgerRepositoryInterface $serviceLedgers,
    ) {
    }

    public function listServiceTypes(): Collection
    {
        return $this->serviceTypes->listActive();
    }

    /**
     * @return Collection<int, SosRequest>
     */
    public function listForUser(User $user): Collection
    {
        return $this->sosRequests->listForUser($user->id);
    }

    public function getForUser(User $user, int $sosRequestId): SosRequest
    {
        return $this->sosRequests->findForUser($user->id, $sosRequestId)
            ?? throw new NotFoundHttpException('SOS request not found.');
    }

    public function create(User $user, StoreSosRequestData $data): SosRequest
    {
        if ($data->vehicleId && ! $this->vehicles->findForUser($user->id, $data->vehicleId)) {
            throw ValidationException::withMessages(['vehicle_id' => __('The selected vehicle was not found.')]);
        }

        $imagePath = $data->image?->store('sos-requests', 'public');

        return DB::transaction(function () use ($user, $data, $imagePath): SosRequest {
            $sosRequest = $this->sosRequests->createForUser($user->id, $data, $imagePath);
            $this->logs->create($sosRequest, null, SosRequestStatus::Pending, $user->id, 'SOS request created.');

            $provider = $this->providers->findNearestAvailable($data->sosServiceTypeId, $data->latitude, $data->longitude);
            if ($provider) {
                $sosRequest = $this->sosRequests->assignProvider($sosRequest, $provider);
                $this->logs->create($sosRequest, SosRequestStatus::Pending, SosRequestStatus::Assigned, null, 'Nearest provider assigned.');

                if ($provider->user_id) {
                    $this->notifications->create(
                        $provider->user_id,
                        'New SOS request',
                        'A new SOS request #' . $sosRequest->id . ' has been assigned to you.',
                        'sos_assigned',
                        ['sos_request_id' => $sosRequest->id]
                    );
                }

                $this->whatsappMessages->createSosProviderNotification($sosRequest);

                if ($provider->workshop_id) {
                    $this->leads->createFromSosRequest($sosRequest);
                }
            }

            return $sosRequest->refresh()->load(['vehicle.brand', 'vehicle.model', 'serviceType', 'assignedProvider.workshop', 'assignedProvider.serviceTypes', 'logs']);
        });
    }

    public function cancel(User $user, int $sosRequestId, SosStatusChangeData $data): SosRequest
    {
        return DB::transaction(fn (): SosRequest => $this->transition(
            $this->getForUser($user, $sosRequestId),
            SosRequestStatus::Cancelled,
            $user,
            $data->notes
        ));
    }

    /**
     * @return Collection<int, SosRequest>
     */
    public function listForProvider(User $user): Collection
    {
        return $this->sosRequests->listForProvider($this->providerForUser($user));
    }

    public function getForProvider(User $user, int $sosRequestId): SosRequest
    {
        return $this->sosRequests->findForProvider($this->providerForUser($user), $sosRequestId)
            ?? throw new NotFoundHttpException('SOS request not found.');
    }

    public function providerTransition(User $user, int $sosRequestId, SosRequestStatus $targetStatus, SosStatusChangeData $data): SosRequest
    {
        return DB::transaction(function () use ($user, $sosRequestId, $targetStatus, $data): SosRequest {
            $sosRequest = $this->getForProvider($user, $sosRequestId);

            if ($targetStatus === SosRequestStatus::Pending) {
                $updated = $this->decline($sosRequest, $user, $data->notes);
            } else {
                $updated = $this->transition($sosRequest, $targetStatus, $user, $data->notes);
            }

            if ($targetStatus === SosRequestStatus::Completed && $updated->vehicle_id) {
                $this->serviceLedgers->createFromCompletedSosRequest($updated);
            }

            if ($targetStatus !== SosRequestStatus::Pending) {
                $this->notifications->create(
                    $updated->user_id,
                    'SOS request updated',
                    'Your SOS request #' . $updated->id . ' is now ' . $targetStatus->value . '.',
                    'sos_status_updated',
                    ['sos_request_id' => $updated->id, 'status' => $targetStatus->value]
                );
            }

            return $updated;
        });
    }

    private function transition(SosRequest $sosRequest, SosRequestStatus $targetStatus, User $actor, ?string $notes): SosRequest
    {
        $oldStatus = $sosRequest->status instanceof SosRequestStatus ? $sosRequest->status : SosRequestStatus::from($sosRequest->status);

        if (! in_array($targetStatus->value, $this->allowedTransitions[$oldStatus->value] ?? [], true)) {
            throw ValidationException::withMessages(['status' => __('Invalid SOS request status transition.')]);
        }

        $updated = $this->sosRequests->updateStatus($sosRequest, $targetStatus);
        $this->logs->create($updated, $oldStatus, $targetStatus, $actor->id, $notes);

        return $updated;
    }

    private function decline(SosRequest $sosRequest, User $actor, ?string $notes): SosRequest
    {
        $oldStatus = $sosRequest->status instanceof SosRequestStatus ? $sosRequest->status : SosRequestStatus::from($sosRequest->status);

        if ($oldStatus !== SosRequestStatus::Assigned) {
            throw ValidationException::withMessages(['status' => __('Invalid SOS request status transition.')]);
        }

        $updated = $this->sosRequests->unassignToPending($sosRequest);
        $this->logs->create($updated, $oldStatus, SosRequestStatus::Pending, $actor->id, $notes ?? 'Provider declined request.');

        return $updated;
    }

    private function providerForUser(User $user)
    {
        $role = $user->role instanceof UserRole ? $user->role : UserRole::from($user->role);
        if ($role !== UserRole::Provider) {
            throw new NotFoundHttpException('SOS provider not found.');
        }

        return $this->providers->findForUser($user->id)
            ?? throw new NotFoundHttpException('SOS provider not found.');
    }
}
