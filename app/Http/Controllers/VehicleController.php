<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vehicle\StoreVehicleRequest;
use App\Http\Requests\Vehicle\UpdateVehicleRequest;
use App\Http\Resources\VehicleResource;
use App\Models\User;
use App\Services\VehicleService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function __construct(private readonly VehicleService $vehicleService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Vehicles retrieved successfully.', [
            'items' => VehicleResource::collection($this->vehicleService->listForUser($user)),
        ]);
    }

    public function store(StoreVehicleRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $vehicle = $this->vehicleService->create($user, $request->toDto());

        return ApiResponse::success('Vehicle created successfully.', [
            'vehicle' => new VehicleResource($vehicle),
        ], 201);
    }

    public function show(int $vehicle, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Vehicle retrieved successfully.', [
            'vehicle' => new VehicleResource($this->vehicleService->getForUser($user, $vehicle)),
        ]);
    }

    public function update(int $vehicle, UpdateVehicleRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Vehicle updated successfully.', [
            'vehicle' => new VehicleResource($this->vehicleService->update($user, $vehicle, $request->toDto())),
        ]);
    }

    public function destroy(int $vehicle, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->vehicleService->delete($user, $vehicle);

        return ApiResponse::success('Vehicle deleted successfully.');
    }
}
