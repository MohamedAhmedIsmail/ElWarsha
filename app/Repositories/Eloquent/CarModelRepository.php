<?php

namespace App\Repositories\Eloquent;

use App\DTOs\LookupQueryData;
use App\Models\CarModel;
use App\Repositories\Contracts\CarModelRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CarModelRepository implements CarModelRepositoryInterface
{
    public function listActive(LookupQueryData $queryData, ?int $brandId = null): Collection|LengthAwarePaginator
    {
        $query = CarModel::query()
            ->active()
            ->with(['brand:id,name,logo,status'])
            ->select(['id', 'car_brand_id', 'name', 'status', 'created_at', 'updated_at'])
            ->when($brandId, function (Builder $query, int $brandId): void {
                $query->where('car_brand_id', $brandId);
            })
            ->when($queryData->search, function (Builder $query, string $search): void {
                $query->where('car_models.name', 'like', '%' . $search . '%');
            })
            ->orderBy('car_models.name');

        return $queryData->paginate
            ? $query->paginate($queryData->perPage)
            : $query->get();
    }
}
