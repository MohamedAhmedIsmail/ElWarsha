<?php

namespace App\Repositories\Contracts;

use App\Enums\WorkshopImageType;
use App\Models\Workshop;
use App\Models\WorkshopImage;
use Illuminate\Database\Eloquent\Collection;

interface WorkshopImageRepositoryInterface
{
    public function create(Workshop $workshop, string $path, WorkshopImageType $type, int $sortOrder): WorkshopImage;

    public function findForWorkshop(Workshop $workshop, int $imageId): ?WorkshopImage;

    public function delete(WorkshopImage $image): void;

    /**
     * @return Collection<int, WorkshopImage>
     */
    public function listForWorkshop(Workshop $workshop): Collection;
}
