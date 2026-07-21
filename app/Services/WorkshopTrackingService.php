<?php

namespace App\Services;

use App\DTOs\Crm\TrackWorkshopEventData;
use App\Enums\LeadSource;
use App\Enums\WorkshopAnalyticsEventType;
use App\Models\User;
use App\Models\WorkshopAnalyticsEvent;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Repositories\Contracts\WorkshopAnalyticsRepositoryInterface;
use App\Repositories\Contracts\WorkshopRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WorkshopTrackingService
{
    public function __construct(
        private readonly WorkshopRepositoryInterface $workshops,
        private readonly WorkshopAnalyticsRepositoryInterface $analytics,
        private readonly LeadRepositoryInterface $leads,
        private readonly VehicleRepositoryInterface $vehicles,
    ) {
    }

    public function track(int $workshopId, WorkshopAnalyticsEventType $eventType, TrackWorkshopEventData $data, ?User $user): WorkshopAnalyticsEvent
    {
        $workshop = $this->workshops->findApproved($workshopId)
            ?? throw new NotFoundHttpException('Workshop not found.');

        if ($data->vehicleId !== null) {
            if (! $user || ! $this->vehicles->findForUser($user->id, $data->vehicleId)) {
                throw ValidationException::withMessages([
                    'vehicle_id' => __('The selected vehicle was not found.'),
                ]);
            }
        }

        return DB::transaction(function () use ($workshop, $eventType, $data, $user): WorkshopAnalyticsEvent {
            $event = $this->analytics->create(
                $workshop->id,
                $user?->id,
                $eventType,
                $data->latitude,
                $data->longitude,
                $data->metadata
            );

            $source = $this->leadSourceForEvent($eventType);

            if ($source !== null) {
                if ($source !== LeadSource::ProfileView) {
                    $this->leads->createFromTracking($workshop, $user?->id, $data->vehicleId, $source);
                } elseif ($user && ! $this->leads->hasProfileViewLeadToday($workshop, $user->id)) {
                    $this->leads->createFromTracking($workshop, $user->id, $data->vehicleId, $source);
                }
            }

            return $event;
        });
    }

    private function leadSourceForEvent(WorkshopAnalyticsEventType $eventType): ?LeadSource
    {
        return match ($eventType) {
            WorkshopAnalyticsEventType::ProfileView => LeadSource::ProfileView,
            WorkshopAnalyticsEventType::CallClick => LeadSource::CallClick,
            WorkshopAnalyticsEventType::WhatsappClick => LeadSource::WhatsappClick,
            WorkshopAnalyticsEventType::DirectionsClick => LeadSource::DirectionsClick,
            default => null,
        };
    }
}
