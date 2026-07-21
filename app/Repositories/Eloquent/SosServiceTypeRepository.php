<?php

namespace App\Repositories\Eloquent;

use App\Models\SosServiceType;
use App\Repositories\Contracts\SosServiceTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SosServiceTypeRepository implements SosServiceTypeRepositoryInterface
{
    public function listActive(): Collection
    {
        return SosServiceType::query()
            ->active()
            ->orderBy('name')
            ->get();
    }
}
