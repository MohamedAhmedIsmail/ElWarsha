<?php

namespace App\Repositories\Eloquent;

use App\Models\ServiceCategory;
use App\Repositories\Contracts\DiagnosisCategoryRepositoryInterface;

class DiagnosisCategoryRepository implements DiagnosisCategoryRepositoryInterface
{
    public function findActiveByName(string $name): ?ServiceCategory
    {
        return ServiceCategory::query()
            ->active()
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->first();
    }
}
