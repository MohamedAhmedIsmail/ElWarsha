<?php

namespace App\Repositories\Eloquent;

use App\DTOs\LookupQueryData;
use App\Models\CarBrand;
use App\Repositories\Contracts\CarBrandRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CarBrandRepository implements CarBrandRepositoryInterface
{
    public function listActive(LookupQueryData $queryData): Collection|LengthAwarePaginator
    {
        $query = CarBrand::query()
            ->active()
            ->select(['id', 'name', 'logo', 'status', 'created_at', 'updated_at'])
            ->when($queryData->search, function (Builder $query, string $search): void {
                $query->where('car_brands.name', 'like', '%' . $search . '%');
            })
            ->orderBy('car_brands.name');

        return $queryData->paginate
            ? $query->paginate($queryData->perPage)
            : $query->get();
    }

    public function findActive(int $id): ?CarBrand
    {
        return CarBrand::query()
            ->active()
            ->whereKey($id)
            ->first();
    }
}
