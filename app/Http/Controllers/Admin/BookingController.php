<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->with(['user', 'vehicle.brand', 'vehicle.model', 'workshop', 'service'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('workshop'), fn ($q) => $q->where('workshop_id', $request->integer('workshop')))
            ->when($request->filled('service'), fn ($q) => $q->where('service_id', $request->integer('service')))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('scheduled_at', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('scheduled_at', '<=', $request->date('date_to')))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'statuses' => BookingStatus::cases(),
            'workshops' => Workshop::query()->orderBy('name')->get(['id', 'name']),
            'services' => Service::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(Booking $booking): View
    {
        return view('admin.bookings.show', [
            'booking' => $booking->load(['user', 'vehicle.brand', 'vehicle.model', 'workshop', 'diagnosis', 'service', 'statusLogs']),
        ]);
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        return $this->setStatus($request, $booking, BookingStatus::Cancelled, 'Booking cancelled.');
    }

    public function complete(Request $request, Booking $booking): RedirectResponse
    {
        return $this->setStatus($request, $booking, BookingStatus::Completed, 'Booking completed.');
    }

    private function setStatus(Request $request, Booking $booking, BookingStatus $status, string $message): RedirectResponse
    {
        $old = $booking->status?->value ?? $booking->status;
        $attributes = ['status' => $status, 'admin_notes' => $request->input('admin_notes')];
        if ($status === BookingStatus::Cancelled) {
            $attributes['cancelled_at'] = now();
        }
        if ($status === BookingStatus::Completed) {
            $attributes['completed_at'] = now();
        }

        $booking->update($attributes);
        $booking->statusLogs()->create([
            'old_status' => $old,
            'new_status' => $status->value,
            'changed_by' => $request->user()->id,
            'notes' => $request->input('admin_notes'),
        ]);

        return back()->with('success', $message);
    }
}
