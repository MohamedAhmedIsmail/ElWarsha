<?php

namespace App\Repositories\Contracts;

use App\DTOs\LookupQueryData;
use App\Models\CarBrand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CarBrandRepositoryInterface
{
    /**
     * @return Collection<int, CarBrand>|LengthAwarePaginator
     */
    public function listActive(LookupQueryData $queryData): Collection|LengthAwarePaginator;

    public function findActive(int $id): ?CarBrand;
}
