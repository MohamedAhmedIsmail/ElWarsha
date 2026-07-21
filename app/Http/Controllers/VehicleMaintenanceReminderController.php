<?php

namespace App\Http\Controllers;

use App\Http\Requests\Maintenance\StoreMaintenanceReminderRequest;
use App\Http\Requests\Maintenance\UpdateMaintenanceReminderRequest;
use App\Http\Resources\VehicleMaintenanceReminderResource;
use App\Models\User;
use App\Services\MaintenanceService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleMaintenanceReminderController extends Controller
{
    public function __construct(private readonly MaintenanceService $maintenanceService)
    {
    }

    public function index(int $vehicle, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Maintenance reminders retrieved successfully.', [
            'items' => VehicleMaintenanceReminderResource::collection($this->maintenanceService->listVehicleReminders($user, $vehicle)),
        ]);
    }

    public function store(int $vehicle, StoreMaintenanceReminderRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Maintenance reminder created successfully.', [
            'maintenance_reminder' => new VehicleMaintenanceReminderResource($this->maintenanceService->createReminder($user, $vehicle, $request->toDto())),
        ], 201);
    }

    public function update(int $reminder, UpdateMaintenanceReminderRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Maintenance reminder updated successfully.', [
            'maintenance_reminder' => new VehicleMaintenanceReminderResource($this->maintenanceService->updateReminder($user, $reminder, $request->toDto())),
        ]);
    }

    public function destroy(int $reminder, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->maintenanceService->deleteReminder($user, $reminder);

        return ApiResponse::success('Maintenance reminder deleted successfully.');
    }

    public function upcoming(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Upcoming maintenance reminders retrieved successfully.', [
            'items' => VehicleMaintenanceReminderResource::collection($this->maintenanceService->upcoming($user)),
        ]);
    }
}
