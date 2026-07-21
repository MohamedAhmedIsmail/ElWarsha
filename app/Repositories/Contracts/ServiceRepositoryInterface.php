<?php

namespace App\Repositories\Contracts;

use App\DTOs\LookupQueryData;
use App\Models\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ServiceRepositoryInterface
{
    /**
     * @return Collection<int, Service>|LengthAwarePaginator
     */
    public function listActive(LookupQueryData $queryData, ?int $categoryId = null): Collection|LengthAwarePaginator;
}
