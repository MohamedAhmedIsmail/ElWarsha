<?php

namespace App\Http\Controllers;

use App\Http\Resources\SosServiceTypeResource;
use App\Services\SosService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class SosServiceTypeController extends Controller
{
    public function __construct(private readonly SosService $sosService)
    {
    }

    public function index(): JsonResponse
    {
        return ApiResponse::success('SOS service types retrieved successfully.', [
            'items' => SosServiceTypeResource::collection($this->sosService->listServiceTypes()),
        ]);
    }
}
