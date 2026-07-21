<?php

namespace App\Http\Controllers;

use App\Enums\SosRequestStatus;
use App\Http\Requests\Sos\SosStatusChangeRequest;
use App\Http\Resources\SosRequestResource;
use App\Models\User;
use App\Services\SosService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProviderSosRequestController extends Controller
{
    public function __construct(private readonly SosService $sosService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Provider SOS requests retrieved successfully.', [
            'items' => SosRequestResource::collection($this->sosService->listForProvider($user)),
        ]);
    }

    public function show(int $sosRequest, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Provider SOS request retrieved successfully.', [
            'sos_request' => new SosRequestResource($this->sosService->getForProvider($user, $sosRequest)),
        ]);
    }

    public function accept(int $sosRequest, SosStatusChangeRequest $request): JsonResponse
    {
        return $this->transition($sosRequest, SosRequestStatus::Accepted, $request, 'SOS request accepted successfully.');
    }

    public function decline(int $sosRequest, SosStatusChangeRequest $request): JsonResponse
    {
        return $this->transition($sosRequest, SosRequestStatus::Pending, $request, 'SOS request declined successfully.');
    }

    public function onTheWay(int $sosRequest, SosStatusChangeRequest $request): JsonResponse
    {
        return $this->transition($sosRequest, SosRequestStatus::OnTheWay, $request, 'SOS provider is on the way.');
    }

    public function arrived(int $sosRequest, SosStatusChangeRequest $request): JsonResponse
    {
        return $this->transition($sosRequest, SosRequestStatus::Arrived, $request, 'SOS provider arrived successfully.');
    }

    public function complete(int $sosRequest, SosStatusChangeRequest $request): JsonResponse
    {
        return $this->transition($sosRequest, SosRequestStatus::Completed, $request, 'SOS request completed successfully.');
    }

    private function transition(int $sosRequest, SosRequestStatus $status, SosStatusChangeRequest $request, string $message): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success($message, [
            'sos_request' => new SosRequestResource($this->sosService->providerTransition($user, $sosRequest, $status, $request->toDto())),
        ]);
    }
}
