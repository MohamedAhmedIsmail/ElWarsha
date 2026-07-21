<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceLedger\StoreServiceLedgerRequest;
use App\Http\Requests\ServiceLedger\UpdateServiceLedgerRequest;
use App\Http\Requests\ServiceLedger\UploadServiceLedgerMediaRequest;
use App\Http\Resources\ServiceLedgerMediaResource;
use App\Http\Resources\ServiceLedgerResource;
use App\Models\User;
use App\Services\ServiceLedgerService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceLedgerController extends Controller
{
    public function __construct(private readonly ServiceLedgerService $serviceLedgerService)
    {
    }

    public function index(int $vehicle, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Service ledger retrieved successfully.', [
            'items' => ServiceLedgerResource::collection($this->serviceLedgerService->listForVehicle($user, $vehicle)),
        ]);
    }

    public function store(int $vehicle, StoreServiceLedgerRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Service ledger entry created successfully.', [
            'service_ledger' => new ServiceLedgerResource($this->serviceLedgerService->create($user, $vehicle, $request->toDto())),
        ], 201);
    }

    public function show(int $ledger, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Service ledger entry retrieved successfully.', [
            'service_ledger' => new ServiceLedgerResource($this->serviceLedgerService->getForUser($user, $ledger)),
        ]);
    }

    public function update(int $ledger, UpdateServiceLedgerRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Service ledger entry updated successfully.', [
            'service_ledger' => new ServiceLedgerResource($this->serviceLedgerService->update($user, $ledger, $request->toDto())),
        ]);
    }

    public function destroy(int $ledger, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->serviceLedgerService->delete($user, $ledger);

        return ApiResponse::success('Service ledger entry deleted successfully.');
    }

    public function uploadMedia(int $ledger, UploadServiceLedgerMediaRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Service ledger media uploaded successfully.', [
            'items' => ServiceLedgerMediaResource::collection($this->serviceLedgerService->uploadMedia($user, $ledger, $request->toDto())),
        ], 201);
    }
}
