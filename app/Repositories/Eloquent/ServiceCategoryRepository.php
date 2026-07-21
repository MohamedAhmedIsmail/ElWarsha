<?php

namespace App\Repositories\Eloquent;

use App\DTOs\LookupQueryData;
use App\Models\ServiceCategory;
use App\Repositories\Contracts\ServiceCategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ServiceCategoryRepository implements ServiceCategoryRepositoryInterface
{
    public function listActive(LookupQueryData $queryData): Collection|LengthAwarePaginator
    {
        $query = ServiceCategory::query()
            ->active()
            ->select(['id', 'name', 'slug', 'icon', 'description', 'status', 'sort_order', 'created_at', 'updated_at'])
            ->when($queryData->search, function (Builder $query, string $search): void {
                $query->where('service_categories.name', 'like', '%' . $search . '%');
            })
            ->orderBy('service_categories.sort_order')
            ->orderBy('service_categories.name');

        return $queryData->paginate
            ? $query->paginate($queryData->perPage)
            : $query->get();
    }

    public function findActive(int $id): ?ServiceCategory
    {
        return ServiceCategory::query()
            ->active()
            ->whereKey($id)
            ->first();
    }
}
