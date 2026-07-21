<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Workshop\WorkshopData;
use App\DTOs\Workshop\WorkshopFilterData;
use App\Enums\WorkshopStatus;
use App\Models\Workshop;
use App\Repositories\Contracts\WorkshopRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class WorkshopRepository implements WorkshopRepositoryInterface
{
    public function searchApproved(WorkshopFilterData $filters, bool $nearby = false): LengthAwarePaginator
    {
        $query = Workshop::query()
            ->approved()
            ->with($this->defaultRelations())
            ->select('workshops.*');

        $this->applyFilters($query, $filters);

        if ($nearby && $filters->lat !== null && $filters->lng !== null) {
            $haversine = '(6371 * acos(cos(radians(?)) * cos(radians(workshops.latitude)) * cos(radians(workshops.longitude) - radians(?)) + sin(radians(?)) * sin(radians(workshops.latitude))))';
            $query
                ->selectRaw($haversine . ' AS distance', [$filters->lat, $filters->lng, $filters->lat])
                ->having('distance', '<=', $filters->radius)
                ->orderBy('distance')
                ->orderByDesc('workshops.rating_avg');
        } else {
            $query->orderByDesc('workshops.is_verified')
                ->orderByDesc('workshops.rating_avg')
                ->orderBy('workshops.name');
        }

        return $query->paginate($filters->perPage);
    }

    public function findApproved(int $workshopId): ?Workshop
    {
        return Workshop::query()
            ->approved()
            ->with([...$this->defaultRelations(), 'workingHours'])
            ->whereKey($workshopId)
            ->first();
    }

    public function findForOwner(int $ownerId): ?Workshop
    {
        return Workshop::query()
            ->ownedBy($ownerId)
            ->with([...$this->defaultRelations(), 'workingHours'])
            ->first();
    }

    public function createForOwner(int $ownerId, WorkshopData $data): Workshop
    {
        $workshop = Workshop::query()->create([
            ...$data->toArray(),
            'owner_id' => $ownerId,
            'status' => WorkshopStatus::Pending,
        ]);

        return $workshop->load([...$this->defaultRelations(), 'workingHours']);
    }

    public function update(Workshop $workshop, WorkshopData $data): Workshop
    {
        $workshop->forceFill($data->toArray())->save();

        return $workshop->refresh()->load([...$this->defaultRelations(), 'workingHours']);
    }

    public function syncServices(Workshop $workshop, array $serviceIds): Workshop
    {
        $workshop->services()->sync($serviceIds);

        return $workshop->refresh()->load([...$this->defaultRelations(), 'workingHours']);
    }

    public function syncBrands(Workshop $workshop, array $brandIds): Workshop
    {
        $workshop->brands()->sync($brandIds);

        return $workshop->refresh()->load([...$this->defaultRelations(), 'workingHours']);
    }

    /**
     * @return array<int, string>
     */
    private function defaultRelations(): array
    {
        return [
            'services:id,service_category_id,name,slug,description,status',
            'services.category:id,name,slug,icon,status,sort_order',
            'brands:id,name,logo,status',
            'images',
        ];
    }

    private function applyFilters(Builder $query, WorkshopFilterData $filters): void
    {
        $query
            ->when($filters->serviceId, fn (Builder $query, int $serviceId) => $query->whereHas('services', fn (Builder $query) => $query->whereKey($serviceId)))
            ->when($filters->categoryId, fn (Builder $query, int $categoryId) => $query->whereHas('services', fn (Builder $query) => $query->where('services.service_category_id', $categoryId)))
            ->when($filters->brandId, fn (Builder $query, int $brandId) => $query->whereHas('brands', fn (Builder $query) => $query->whereKey($brandId)))
            ->when($filters->city, fn (Builder $query, string $city) => $query->where('workshops.city', $city))
            ->when($filters->area, fn (Builder $query, string $area) => $query->where('workshops.area', $area))
            ->when($filters->rating !== null, fn (Builder $query) => $query->where('workshops.rating_avg', '>=', $filters->rating))
            ->when($filters->isVerified !== null, fn (Builder $query) => $query->where('workshops.is_verified', $filters->isVerified))
            ->when($filters->acceptsBooking !== null, fn (Builder $query) => $query->where('workshops.accepts_booking', $filters->acceptsBooking))
            ->when($filters->acceptsSos !== null, fn (Builder $query) => $query->where('workshops.accepts_sos', $filters->acceptsSos))
            ->when($filters->search, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('workshops.name', 'like', '%' . $search . '%')
                        ->orWhere('workshops.description', 'like', '%' . $search . '%')
                        ->orWhere('workshops.address', 'like', '%' . $search . '%');
                });
            });

        if ($filters->openNow === true) {
            $day = strtolower(now()->format('l'));
            $time = now()->format('H:i:s');

            $query->whereHas('workingHours', function (Builder $query) use ($day, $time): void {
                $query->where('day_of_week', $day)
                    ->where('is_closed', false)
                    ->where('opens_at', '<=', $time)
                    ->where('closes_at', '>=', $time);
            });
        }
    }
}
