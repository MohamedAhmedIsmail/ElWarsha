<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sos\SosStatusChangeRequest;
use App\Http\Requests\Sos\StoreSosRequestRequest;
use App\Http\Resources\SosRequestResource;
use App\Models\User;
use App\Services\SosService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SosRequestController extends Controller
{
    public function __construct(private readonly SosService $sosService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('SOS requests retrieved successfully.', [
            'items' => SosRequestResource::collection($this->sosService->listForUser($user)),
        ]);
    }

    public function store(StoreSosRequestRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('SOS request created successfully.', [
            'sos_request' => new SosRequestResource($this->sosService->create($user, $request->toDto())),
        ], 201);
    }

    public function show(int $sosRequest, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('SOS request retrieved successfully.', [
            'sos_request' => new SosRequestResource($this->sosService->getForUser($user, $sosRequest)),
        ]);
    }

    public function cancel(int $sosRequest, SosStatusChangeRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('SOS request cancelled successfully.', [
            'sos_request' => new SosRequestResource($this->sosService->cancel($user, $sosRequest, $request->toDto())),
        ]);
    }
}
