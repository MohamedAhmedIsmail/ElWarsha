<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Vehicle\StoreVehicleData;
use App\DTOs\Vehicle\UpdateVehicleData;
use App\Enums\RecordStatus;
use App\Models\Vehicle;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class VehicleRepository implements VehicleRepositoryInterface
{
    public function listForUser(int $userId): Collection
    {
        return Vehicle::query()
            ->ownedBy($userId)
            ->with(['brand:id,name,logo,status', 'model:id,car_brand_id,name,status'])
            ->latest('id')
            ->get();
    }

    public function findForUser(int $userId, int $vehicleId): ?Vehicle
    {
        return Vehicle::query()
            ->ownedBy($userId)
            ->with(['brand:id,name,logo,status', 'model:id,car_brand_id,name,status'])
            ->whereKey($vehicleId)
            ->first();
    }

    public function createForUser(int $userId, StoreVehicleData $data): Vehicle
    {
        $vehicle = Vehicle::query()->create([
            ...$data->toArray(),
            'user_id' => $userId,
            'status' => RecordStatus::Active,
        ]);

        return $vehicle->load(['brand:id,name,logo,status', 'model:id,car_brand_id,name,status']);
    }

    public function update(Vehicle $vehicle, UpdateVehicleData $data): Vehicle
    {
        $vehicle->forceFill($data->toArray())->save();

        return $vehicle->refresh()->load(['brand:id,name,logo,status', 'model:id,car_brand_id,name,status']);
    }

    public function delete(Vehicle $vehicle): void
    {
        $vehicle->delete();
    }
}
