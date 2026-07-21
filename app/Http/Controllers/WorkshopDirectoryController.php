<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaginationRequest;
use App\Http\Requests\Workshop\WorkshopIndexRequest;
use App\Http\Requests\Workshop\WorkshopNearbyRequest;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\WorkshopResource;
use App\Models\User;
use App\Services\WorkshopDirectoryService;
use App\Support\ApiResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class WorkshopDirectoryController extends Controller
{
    public function __construct(private readonly WorkshopDirectoryService $workshopDirectoryService)
    {
    }

    public function index(WorkshopIndexRequest $request): JsonResponse
    {
        return $this->respondWithPaginator(
            'Workshops retrieved successfully.',
            WorkshopResource::class,
            $this->workshopDirectoryService->search($request->toDto())
        );
    }

    public function nearby(WorkshopNearbyRequest $request): JsonResponse
    {
        return $this->respondWithPaginator(
            'Nearby workshops retrieved successfully.',
            WorkshopResource::class,
            $this->workshopDirectoryService->nearby($request->toDto())
        );
    }

    public function show(int $workshop, Request $request): JsonResponse
    {
        /** @var User|null $viewer */
        $viewer = Auth::guard('sanctum')->user();

        return ApiResponse::success('Workshop retrieved successfully.', [
            'workshop' => new WorkshopResource($this->workshopDirectoryService->show($workshop, $viewer)),
        ]);
    }

    public function services(int $workshop): JsonResponse
    {
        return ApiResponse::success('Workshop services retrieved successfully.', [
            'items' => ServiceResource::collection($this->workshopDirectoryService->services($workshop)->services),
        ]);
    }

    public function reviews(int $workshop, PaginationRequest $request): JsonResponse
    {
        return $this->respondWithPaginator(
            'Workshop reviews retrieved successfully.',
            ReviewResource::class,
            $this->workshopDirectoryService->reviews($workshop, $request->perPage())
        );
    }

    /**
     * @param class-string<JsonResource> $resourceClass
     */
    private function respondWithPaginator(string $message, string $resourceClass, LengthAwarePaginator $paginator): JsonResponse
    {
        return ApiResponse::success($message, [
            'items' => $resourceClass::collection(collect($paginator->items())),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }
}
