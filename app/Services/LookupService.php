<?php

namespace App\Services;

use App\DTOs\LookupQueryData;
use App\Models\CarBrand;
use App\Models\ServiceCategory;
use App\Repositories\Contracts\CarBrandRepositoryInterface;
use App\Repositories\Contracts\CarModelRepositoryInterface;
use App\Repositories\Contracts\ServiceCategoryRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LookupService
{
    public function __construct(
        private readonly CarBrandRepositoryInterface $carBrands,
        private readonly CarModelRepositoryInterface $carModels,
        private readonly ServiceCategoryRepositoryInterface $serviceCategories,
        private readonly ServiceRepositoryInterface $services,
    ) {
    }

    public function listCarBrands(LookupQueryData $queryData): Collection|LengthAwarePaginator
    {
        return $this->carBrands->listActive($queryData);
    }

    public function listCarModels(LookupQueryData $queryData): Collection|LengthAwarePaginator
    {
        return $this->carModels->listActive($queryData);
    }

    public function listModelsForBrand(int $brandId, LookupQueryData $queryData): Collection|LengthAwarePaginator
    {
        $brand = $this->getActiveCarBrand($brandId);

        return $this->carModels->listActive($queryData, $brand->id);
    }

    public function listServiceCategories(LookupQueryData $queryData): Collection|LengthAwarePaginator
    {
        return $this->serviceCategories->listActive($queryData);
    }

    public function listServices(LookupQueryData $queryData): Collection|LengthAwarePaginator
    {
        return $this->services->listActive($queryData);
    }

    public function listServicesForCategory(int $categoryId, LookupQueryData $queryData): Collection|LengthAwarePaginator
    {
        $category = $this->getActiveServiceCategory($categoryId);

        return $this->services->listActive($queryData, $category->id);
    }

    private function getActiveCarBrand(int $brandId): CarBrand
    {
        return $this->carBrands->findActive($brandId)
            ?? throw new NotFoundHttpException('Car brand not found.');
    }

    private function getActiveServiceCategory(int $categoryId): ServiceCategory
    {
        return $this->serviceCategories->findActive($categoryId)
            ?? throw new NotFoundHttpException('Service category not found.');
    }
}
