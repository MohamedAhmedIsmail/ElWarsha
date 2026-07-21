<?php

namespace App\Http\Controllers;

use App\Http\Requests\LookupIndexRequest;
use App\Http\Resources\CarBrandResource;
use App\Http\Resources\CarModelResource;
use App\Http\Resources\ServiceCategoryResource;
use App\Http\Resources\ServiceResource;
use App\Services\LookupService;
use App\Support\ApiResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class LookupController extends Controller
{
    public function __construct(private readonly LookupService $lookupService)
    {
    }

    public function carBrands(LookupIndexRequest $request): JsonResponse
    {
        return $this->respondWithCollection(
            'Car brands retrieved successfully.',
            CarBrandResource::class,
            $this->lookupService->listCarBrands($request->toDto())
        );
    }

    public function carModels(LookupIndexRequest $request): JsonResponse
    {
        return $this->respondWithCollection(
            'Car models retrieved successfully.',
            CarModelResource::class,
            $this->lookupService->listCarModels($request->toDto())
        );
    }

    public function carBrandModels(int $brand, LookupIndexRequest $request): JsonResponse
    {
        return $this->respondWithCollection(
            'Car brand models retrieved successfully.',
            CarModelResource::class,
            $this->lookupService->listModelsForBrand($brand, $request->toDto())
        );
    }

    public function serviceCategories(LookupIndexRequest $request): JsonResponse
    {
        return $this->respondWithCollection(
            'Service categories retrieved successfully.',
            ServiceCategoryResource::class,
            $this->lookupService->listServiceCategories($request->toDto())
        );
    }

    public function services(LookupIndexRequest $request): JsonResponse
    {
        return $this->respondWithCollection(
            'Services retrieved successfully.',
            ServiceResource::class,
            $this->lookupService->listServices($request->toDto())
        );
    }

    public function serviceCategoryServices(int $category, LookupIndexRequest $request): JsonResponse
    {
        return $this->respondWithCollection(
            'Service category services retrieved successfully.',
            ServiceResource::class,
            $this->lookupService->listServicesForCategory($category, $request->toDto())
        );
    }

    /**
     * @param class-string<JsonResource> $resourceClass
     * @param Collection<int, mixed>|LengthAwarePaginator $results
     */
    private function respondWithCollection(string $message, string $resourceClass, Collection|LengthAwarePaginator $results): JsonResponse
    {
        if ($results instanceof LengthAwarePaginator) {
            return ApiResponse::success($message, [
                'items' => $resourceClass::collection(collect($results->items())),
                'meta' => [
                    'current_page' => $results->currentPage(),
                    'last_page' => $results->lastPage(),
                    'per_page' => $results->perPage(),
                    'total' => $results->total(),
                ],
            ]);
        }

        return ApiResponse::success($message, [
            'items' => $resourceClass::collection($results),
        ]);
    }
}
