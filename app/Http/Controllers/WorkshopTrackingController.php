<?php

namespace App\Http\Controllers;

use App\Enums\WorkshopAnalyticsEventType;
use App\Http\Requests\Crm\TrackWorkshopEventRequest;
use App\Models\User;
use App\Services\WorkshopTrackingService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WorkshopTrackingController extends Controller
{
    public function __construct(private readonly WorkshopTrackingService $workshopTrackingService)
    {
    }

    public function view(int $workshop, TrackWorkshopEventRequest $request): JsonResponse
    {
        return $this->track($workshop, WorkshopAnalyticsEventType::ProfileView, $request);
    }

    public function call(int $workshop, TrackWorkshopEventRequest $request): JsonResponse
    {
        return $this->track($workshop, WorkshopAnalyticsEventType::CallClick, $request);
    }

    public function whatsapp(int $workshop, TrackWorkshopEventRequest $request): JsonResponse
    {
        return $this->track($workshop, WorkshopAnalyticsEventType::WhatsappClick, $request);
    }

    public function directions(int $workshop, TrackWorkshopEventRequest $request): JsonResponse
    {
        return $this->track($workshop, WorkshopAnalyticsEventType::DirectionsClick, $request);
    }

    private function track(int $workshop, WorkshopAnalyticsEventType $eventType, TrackWorkshopEventRequest $request): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::guard('sanctum')->user();
        $event = $this->workshopTrackingService->track($workshop, $eventType, $request->toDto(), $user);

        return ApiResponse::success('Workshop action tracked successfully.', [
            'event' => [
                'id' => $event->id,
                'event_type' => $event->event_type->value,
                'workshop_id' => $event->workshop_id,
                'user_id' => $event->user_id,
                'latitude' => $event->latitude === null ? null : (float) $event->latitude,
                'longitude' => $event->longitude === null ? null : (float) $event->longitude,
                'metadata' => $event->metadata,
                'created_at' => $event->created_at?->toISOString(),
            ],
        ], 201);
    }
}
