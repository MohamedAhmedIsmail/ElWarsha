<?php

namespace App\Repositories\Eloquent;

use App\DTOs\LookupQueryData;
use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function listActive(LookupQueryData $queryData, ?int $categoryId = null): Collection|LengthAwarePaginator
    {
        $query = Service::query()
            ->active()
            ->whereHas('category', function (Builder $query): void {
                $query->active();
            })
            ->with(['category:id,name,slug,icon,status,sort_order'])
            ->select(['id', 'service_category_id', 'name', 'slug', 'description', 'status', 'created_at', 'updated_at'])
            ->when($categoryId, function (Builder $query, int $categoryId): void {
                $query->where('service_category_id', $categoryId);
            })
            ->when($queryData->search, function (Builder $query, string $search): void {
                $query->where('services.name', 'like', '%' . $search . '%');
            })
            ->join('service_categories', 'services.service_category_id', '=', 'service_categories.id')
            ->orderBy('service_categories.sort_order')
            ->orderBy('services.name')
            ->select('services.*');

        return $queryData->paginate
            ? $query->paginate($queryData->perPage)
            : $query->get();
    }
}
