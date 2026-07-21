<?php

namespace App\Repositories\Contracts;

use App\DTOs\LookupQueryData;
use App\Models\CarModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CarModelRepositoryInterface
{
    /**
     * @return Collection<int, CarModel>|LengthAwarePaginator
     */
    public function listActive(LookupQueryData $queryData, ?int $brandId = null): Collection|LengthAwarePaginator;

    public function existsForBrand(int $modelId, int $brandId): bool;
}
