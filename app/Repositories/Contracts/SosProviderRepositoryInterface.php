<?php

namespace App\Repositories\Contracts;

use App\Models\SosProvider;

interface SosProviderRepositoryInterface
{
    public function findNearestAvailable(int $serviceTypeId, float $latitude, float $longitude): ?SosProvider;

    public function findForUser(int $userId): ?SosProvider;
}
