<?php

namespace App\Repositories\Contracts;

use App\Models\ServiceCategory;

interface DiagnosisCategoryRepositoryInterface
{
    public function findActiveByName(string $name): ?ServiceCategory;
}
