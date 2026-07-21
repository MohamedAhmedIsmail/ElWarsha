<?php

namespace App\Repositories\Eloquent;

use App\Enums\WorkshopImageType;
use App\Models\Workshop;
use App\Models\WorkshopImage;
use App\Repositories\Contracts\WorkshopImageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class WorkshopImageRepository implements WorkshopImageRepositoryInterface
{
    public function create(Workshop $workshop, string $path, WorkshopImageType $type, int $sortOrder): WorkshopImage
    {
        return $workshop->images()->create([
            'image_path' => $path,
            'type' => $type,
            'sort_order' => $sortOrder,
        ]);
    }

    public function findForWorkshop(Workshop $workshop, int $imageId): ?WorkshopImage
    {
        return $workshop->images()
            ->whereKey($imageId)
            ->first();
    }

    public function delete(WorkshopImage $image): void
    {
        $image->delete();
    }

    public function listForWorkshop(Workshop $workshop): Collection
    {
        return $workshop->images()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
