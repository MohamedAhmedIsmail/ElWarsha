<?php

namespace App\Repositories\Contracts;

use App\Models\Workshop;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReviewRepositoryInterface
{
    public function listPublishedForWorkshop(Workshop $workshop, int $perPage): LengthAwarePaginator;
}
