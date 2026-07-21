<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Http\Requests\Booking\BookingStatusChangeRequest;
use App\Http\Resources\BookingResource;
use App\Models\User;
use App\Services\BookingService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkshopBookingController extends Controller
{
    public function __construct(private readonly BookingService $bookingService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Workshop bookings retrieved successfully.', [
            'items' => BookingResource::collection($this->bookingService->listForWorkshopOwner($user)),
        ]);
    }

    public function show(int $booking, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Workshop booking retrieved successfully.', [
            'booking' => new BookingResource($this->bookingService->getForWorkshopOwner($user, $booking)),
        ]);
    }

    public function accept(int $booking, BookingStatusChangeRequest $request): JsonResponse
    {
        return $this->transition($booking, BookingStatus::Accepted, $request, 'Booking accepted successfully.');
    }

    public function decline(int $booking, BookingStatusChangeRequest $request): JsonResponse
    {
        return $this->transition($booking, BookingStatus::Declined, $request, 'Booking declined successfully.');
    }

    public function start(int $booking, BookingStatusChangeRequest $request): JsonResponse
    {
        return $this->transition($booking, BookingStatus::InProgress, $request, 'Booking started successfully.');
    }

    public function complete(int $booking, BookingStatusChangeRequest $request): JsonResponse
    {
        return $this->transition($booking, BookingStatus::Completed, $request, 'Booking completed successfully.');
    }

    private function transition(int $booking, BookingStatus $status, BookingStatusChangeRequest $request, string $message): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success($message, [
            'booking' => new BookingResource($this->bookingService->workshopTransition($user, $booking, $status, $request->toDto())),
        ]);
    }
}
