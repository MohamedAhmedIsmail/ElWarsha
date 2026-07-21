<?php

namespace App\Repositories\Contracts;

use App\DTOs\Vehicle\StoreVehicleData;
use App\DTOs\Vehicle\UpdateVehicleData;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;

interface VehicleRepositoryInterface
{
    /**
     * @return Collection<int, Vehicle>
     */
    public function listForUser(int $userId): Collection;

    public function findForUser(int $userId, int $vehicleId): ?Vehicle;

    public function createForUser(int $userId, StoreVehicleData $data): Vehicle;

    public function update(Vehicle $vehicle, UpdateVehicleData $data): Vehicle;

    public function delete(Vehicle $vehicle): void;
}
