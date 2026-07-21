<?php

namespace App\Http\Controllers;

use App\Http\Resources\MaintenanceItemResource;
use App\Services\MaintenanceService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class MaintenanceItemController extends Controller
{
    public function __construct(private readonly MaintenanceService $maintenanceService)
    {
    }

    public function index(): JsonResponse
    {
        return ApiResponse::success('Maintenance items retrieved successfully.', [
            'items' => MaintenanceItemResource::collection($this->maintenanceService->listItems()),
        ]);
    }
}
