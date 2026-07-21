<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Sos\StoreSosRequestData;
use App\Enums\SosRequestStatus;
use App\Models\SosProvider;
use App\Models\SosRequest;
use App\Repositories\Contracts\SosRequestRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SosRequestRepository implements SosRequestRepositoryInterface
{
    public function listForUser(int $userId): Collection
    {
        return SosRequest::query()
            ->ownedBy($userId)
            ->with($this->relations())
            ->latest('id')
            ->get();
    }

    public function findForUser(int $userId, int $sosRequestId): ?SosRequest
    {
        return SosRequest::query()
            ->ownedBy($userId)
            ->with($this->relations())
            ->whereKey($sosRequestId)
            ->first();
    }

    public function listForProvider(SosProvider $provider): Collection
    {
        return $provider->requests()
            ->with($this->relations())
            ->latest('id')
            ->get();
    }

    public function findForProvider(SosProvider $provider, int $sosRequestId): ?SosRequest
    {
        return $provider->requests()
            ->with($this->relations())
            ->whereKey($sosRequestId)
            ->first();
    }

    public function createForUser(int $userId, StoreSosRequestData $data, ?string $imagePath): SosRequest
    {
        $sosRequest = SosRequest::query()->create([
            'user_id' => $userId,
            'vehicle_id' => $data->vehicleId,
            'sos_service_type_id' => $data->sosServiceTypeId,
            'description' => $data->description,
            'image_path' => $imagePath,
            'latitude' => $data->latitude,
            'longitude' => $data->longitude,
            'urgency' => $data->urgency,
            'status' => SosRequestStatus::Pending,
        ]);

        return $sosRequest->load($this->relations());
    }

    public function assignProvider(SosRequest $sosRequest, SosProvider $provider): SosRequest
    {
        $sosRequest->forceFill([
            'assigned_provider_id' => $provider->id,
            'status' => SosRequestStatus::Assigned,
        ])->save();

        return $sosRequest->refresh()->load($this->relations());
    }

    public function updateStatus(SosRequest $sosRequest, SosRequestStatus $status): SosRequest
    {
        $attributes = ['status' => $status];

        if ($status === SosRequestStatus::Accepted) {
            $attributes['accepted_at'] = now();
        }
        if ($status === SosRequestStatus::Arrived) {
            $attributes['arrived_at'] = now();
        }
        if ($status === SosRequestStatus::Completed) {
            $attributes['completed_at'] = now();
        }
        if ($status === SosRequestStatus::Cancelled) {
            $attributes['cancelled_at'] = now();
        }

        $sosRequest->forceFill($attributes)->save();

        return $sosRequest->refresh()->load($this->relations());
    }

    public function unassignToPending(SosRequest $sosRequest): SosRequest
    {
        $sosRequest->forceFill([
            'assigned_provider_id' => null,
            'status' => SosRequestStatus::Pending,
        ])->save();

        return $sosRequest->refresh()->load($this->relations());
    }

    /**
     * @return array<int, string>
     */
    private function relations(): array
    {
        return [
            'vehicle.brand',
            'vehicle.model',
            'serviceType',
            'assignedProvider.workshop',
            'assignedProvider.serviceTypes',
            'logs',
            'user',
        ];
    }
}
