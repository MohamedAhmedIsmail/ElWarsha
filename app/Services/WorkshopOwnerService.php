<?php

namespace App\Services;

use App\DTOs\Workshop\WorkshopData;
use App\DTOs\Workshop\WorkshopImageUploadData;
use App\DTOs\Workshop\WorkshopSyncData;
use App\DTOs\Workshop\WorkingHoursData;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopImage;
use App\Repositories\Contracts\WorkshopImageRepositoryInterface;
use App\Repositories\Contracts\WorkshopRepositoryInterface;
use App\Repositories\Contracts\WorkshopWorkingHourRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WorkshopOwnerService
{
    public function __construct(
        private readonly WorkshopRepositoryInterface $workshops,
        private readonly WorkshopImageRepositoryInterface $images,
        private readonly WorkshopWorkingHourRepositoryInterface $workingHours,
    ) {
    }

    public function register(User $owner, WorkshopData $data): Workshop
    {
        if ($this->workshops->findForOwner($owner->id)) {
            throw ValidationException::withMessages([
                'workshop' => __('This owner already has a workshop profile.'),
            ]);
        }

        return DB::transaction(fn (): Workshop => $this->workshops->createForOwner($owner->id, $data));
    }

    public function profile(User $owner): Workshop
    {
        return $this->getOwnedWorkshop($owner);
    }

    public function updateProfile(User $owner, WorkshopData $data): Workshop
    {
        $workshop = $this->getOwnedWorkshop($owner);

        return DB::transaction(fn (): Workshop => $this->workshops->update($workshop, $data));
    }

    /**
     * @return Collection<int, WorkshopImage>
     */
    public function uploadImages(User $owner, WorkshopImageUploadData $data): Collection
    {
        $workshop = $this->getOwnedWorkshop($owner);

        return DB::transaction(function () use ($workshop, $data): Collection {
            $created = new Collection();

            foreach ($data->images as $image) {
                $path = $image->store("workshops/{$workshop->id}", 'public');
                $created->push($this->images->create($workshop, $path, $data->type, $data->sortOrder));
            }

            return $created;
        });
    }

    public function deleteImage(User $owner, int $imageId): void
    {
        $workshop = $this->getOwnedWorkshop($owner);
        $image = $this->images->findForWorkshop($workshop, $imageId)
            ?? throw new NotFoundHttpException('Workshop image not found.');

        DB::transaction(function () use ($image): void {
            $path = $image->image_path;
            $this->images->delete($image);
            Storage::disk('public')->delete($path);
        });
    }

    public function syncServices(User $owner, WorkshopSyncData $data): Workshop
    {
        $workshop = $this->getOwnedWorkshop($owner);

        return DB::transaction(fn (): Workshop => $this->workshops->syncServices($workshop, $data->ids));
    }

    public function syncBrands(User $owner, WorkshopSyncData $data): Workshop
    {
        $workshop = $this->getOwnedWorkshop($owner);

        return DB::transaction(fn (): Workshop => $this->workshops->syncBrands($workshop, $data->ids));
    }

    public function syncWorkingHours(User $owner, WorkingHoursData $data): Workshop
    {
        $workshop = $this->getOwnedWorkshop($owner);

        return DB::transaction(fn (): Workshop => $this->workingHours->sync($workshop, $data->hours));
    }

    private function getOwnedWorkshop(User $owner): Workshop
    {
        return $this->workshops->findForOwner($owner->id)
            ?? throw new NotFoundHttpException('Workshop profile not found.');
    }
}
