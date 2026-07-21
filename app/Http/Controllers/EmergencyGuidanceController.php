<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmergencyGuidance\StoreEmergencyGuidanceRequest;
use App\Http\Resources\EmergencyGuidanceResource;
use App\Models\User;
use App\Services\EmergencyGuidanceService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class EmergencyGuidanceController extends Controller
{
    public function __construct(private readonly EmergencyGuidanceService $emergencyGuidanceService)
    {
    }

    public function store(StoreEmergencyGuidanceRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Emergency guidance generated successfully.', [
            'emergency_guidance' => new EmergencyGuidanceResource($this->emergencyGuidanceService->create($user, $request->toDto())),
        ], 201);
    }
}
