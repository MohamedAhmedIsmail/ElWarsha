<?php

namespace App\Services;

use App\DTOs\Vehicle\StoreVehicleData;
use App\DTOs\Vehicle\UpdateVehicleData;
use App\Models\User;
use App\Models\Vehicle;
use App\Repositories\Contracts\CarModelRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VehicleService
{
    public function __construct(
        private readonly VehicleRepositoryInterface $vehicles,
        private readonly CarModelRepositoryInterface $carModels,
    ) {
    }

    /**
     * @return Collection<int, Vehicle>
     */
    public function listForUser(User $user): Collection
    {
        return $this->vehicles->listForUser($user->id);
    }

    public function getForUser(User $user, int $vehicleId): Vehicle
    {
        return $this->vehicles->findForUser($user->id, $vehicleId)
            ?? throw new NotFoundHttpException('Vehicle not found.');
    }

    public function create(User $user, StoreVehicleData $data): Vehicle
    {
        return DB::transaction(fn (): Vehicle => $this->vehicles->createForUser($user->id, $data));
    }

    public function update(User $user, int $vehicleId, UpdateVehicleData $data): Vehicle
    {
        $vehicle = $this->getForUser($user, $vehicleId);
        $this->ensureModelMatchesBrand($vehicle, $data);

        return DB::transaction(fn (): Vehicle => $this->vehicles->update($vehicle, $data));
    }

    public function delete(User $user, int $vehicleId): void
    {
        $vehicle = $this->getForUser($user, $vehicleId);

        DB::transaction(fn () => $this->vehicles->delete($vehicle));
    }

    private function ensureModelMatchesBrand(Vehicle $vehicle, UpdateVehicleData $data): void
    {
        $attributes = $data->toArray();

        if (! array_key_exists('car_model_id', $attributes) && ! array_key_exists('car_brand_id', $attributes)) {
            return;
        }

        $brandId = (int) ($attributes['car_brand_id'] ?? $vehicle->car_brand_id);
        $modelId = (int) ($attributes['car_model_id'] ?? $vehicle->car_model_id);

        if (! $this->carModels->existsForBrand($modelId, $brandId)) {
            throw ValidationException::withMessages([
                'car_model_id' => __('The selected car model does not belong to the selected brand.'),
            ]);
        }
    }
}
