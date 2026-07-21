<?php

namespace App\Repositories\Contracts;

use App\DTOs\Workshop\WorkshopData;
use App\DTOs\Workshop\WorkshopFilterData;
use App\Models\Workshop;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface WorkshopRepositoryInterface
{
    public function searchApproved(WorkshopFilterData $filters, bool $nearby = false): LengthAwarePaginator;

    public function findApproved(int $workshopId): ?Workshop;

    public function findForOwner(int $ownerId): ?Workshop;

    public function createForOwner(int $ownerId, WorkshopData $data): Workshop;

    public function update(Workshop $workshop, WorkshopData $data): Workshop;

    /**
     * @param array<int, int> $serviceIds
     */
    public function syncServices(Workshop $workshop, array $serviceIds): Workshop;

    /**
     * @param array<int, int> $brandIds
     */
    public function syncBrands(Workshop $workshop, array $brandIds): Workshop;
}
