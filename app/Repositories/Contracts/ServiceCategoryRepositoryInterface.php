<?php

namespace App\Repositories\Contracts;

use App\DTOs\LookupQueryData;
use App\Models\ServiceCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ServiceCategoryRepositoryInterface
{
    /**
     * @return Collection<int, ServiceCategory>|LengthAwarePaginator
     */
    public function listActive(LookupQueryData $queryData): Collection|LengthAwarePaginator;

    public function findActive(int $id): ?ServiceCategory;
}
