<?php

namespace App\Services;

use App\DTOs\Workshop\WorkshopFilterData;
use App\Enums\WorkshopAnalyticsEventType;
use App\Models\User;
use App\Models\Workshop;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Repositories\Contracts\WorkshopAnalyticsRepositoryInterface;
use App\Repositories\Contracts\WorkshopRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WorkshopDirectoryService
{
    public function __construct(
        private readonly WorkshopRepositoryInterface $workshops,
        private readonly ReviewRepositoryInterface $reviews,
        private readonly WorkshopAnalyticsRepositoryInterface $analytics,
    ) {
    }

    public function search(WorkshopFilterData $filters): LengthAwarePaginator
    {
        return $this->workshops->searchApproved($filters);
    }

    public function nearby(WorkshopFilterData $filters): LengthAwarePaginator
    {
        return $this->workshops->searchApproved($filters, nearby: true);
    }

    public function show(int $workshopId, ?User $viewer): Workshop
    {
        $workshop = $this->getApproved($workshopId);

        if ($viewer) {
            $this->analytics->create($workshop->id, $viewer->id, WorkshopAnalyticsEventType::ProfileView);
        }

        return $workshop;
    }

    public function services(int $workshopId): Workshop
    {
        return $this->getApproved($workshopId)->load(['services.category']);
    }

    public function reviews(int $workshopId, int $perPage): LengthAwarePaginator
    {
        return $this->reviews->listPublishedForWorkshop($this->getApproved($workshopId), $perPage);
    }

    private function getApproved(int $workshopId): Workshop
    {
        return $this->workshops->findApproved($workshopId)
            ?? throw new NotFoundHttpException('Workshop not found.');
    }
}
