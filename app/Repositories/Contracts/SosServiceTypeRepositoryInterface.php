<?php

namespace App\Repositories\Contracts;

use App\Models\SosServiceType;
use Illuminate\Database\Eloquent\Collection;

interface SosServiceTypeRepositoryInterface
{
    /**
     * @return Collection<int, SosServiceType>
     */
    public function listActive(): Collection;

    public function findActiveBySlug(string $slug): ?SosServiceType;
}
