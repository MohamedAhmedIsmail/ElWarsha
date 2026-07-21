<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlanResource;
use App\Services\PlanService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class PlanController extends Controller
{
    public function __construct(private readonly PlanService $planService)
    {
    }

    public function index(): JsonResponse
    {
        return ApiResponse::success('Plans retrieved successfully.', [
            'items' => PlanResource::collection($this->planService->listActive()),
        ]);
    }
}
