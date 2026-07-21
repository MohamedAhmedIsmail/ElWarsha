<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\BookingStatusChangeRequest;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\User;
use App\Services\BookingService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(private readonly BookingService $bookingService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Bookings retrieved successfully.', [
            'items' => BookingResource::collection($this->bookingService->listForUser($user)),
        ]);
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Booking created successfully.', [
            'booking' => new BookingResource($this->bookingService->create($user, $request->toDto())),
        ], 201);
    }

    public function show(int $booking, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Booking retrieved successfully.', [
            'booking' => new BookingResource($this->bookingService->getForUser($user, $booking)),
        ]);
    }

    public function cancel(int $booking, BookingStatusChangeRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Booking cancelled successfully.', [
            'booking' => new BookingResource($this->bookingService->cancel($user, $booking, $request->toDto())),
        ]);
    }
}
