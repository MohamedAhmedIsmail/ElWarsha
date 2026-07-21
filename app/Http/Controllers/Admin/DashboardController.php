<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\SosRequestStatus;
use App\Enums\SubscriptionStatus;
use App\Enums\WorkshopStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Diagnosis;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Review;
use App\Models\SosRequest;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'kpis' => $this->kpis(),
            'latestPendingWorkshops' => Workshop::query()
                ->where('status', WorkshopStatus::Pending->value)
                ->latest('id')
                ->limit(5)
                ->get(),
            'latestBookings' => Booking::query()
                ->with(['user:id,name,phone', 'workshop:id,name'])
                ->latest('id')
                ->limit(5)
                ->get(),
            'latestSosRequests' => SosRequest::query()
                ->with(['user:id,name,phone', 'serviceType:id,name'])
                ->latest('id')
                ->limit(5)
                ->get(),
            'latestPayments' => Payment::query()
                ->with(['workshop:id,name', 'subscription.plan:id,name'])
                ->latest('id')
                ->limit(5)
                ->get(),
            'topWorkshopsByLeads' => Workshop::query()
                ->withCount('leads')
                ->orderByDesc('leads_count')
                ->limit(5)
                ->get(['id', 'name', 'city', 'area']),
        ]);
    }

    /**
     * @return array<string, array{label: string, value: int|string, tone: string}>
     */
    private function kpis(): array
    {
        $monthlyRevenue = Payment::query()
            ->where('status', PaymentStatus::Approved->value)
            ->whereBetween('approved_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        return [
            'users' => ['label' => 'Total Users', 'value' => User::query()->count(), 'tone' => 'primary'],
            'vehicles' => ['label' => 'Total Vehicles', 'value' => Vehicle::query()->count(), 'tone' => 'info'],
            'workshops' => ['label' => 'Total Workshops', 'value' => Workshop::query()->count(), 'tone' => 'dark'],
            'pending_workshops' => ['label' => 'Pending Workshops', 'value' => Workshop::query()->where('status', WorkshopStatus::Pending->value)->count(), 'tone' => 'warning'],
            'approved_workshops' => ['label' => 'Approved Workshops', 'value' => Workshop::query()->where('status', WorkshopStatus::Approved->value)->count(), 'tone' => 'success'],
            'diagnoses' => ['label' => 'Total Diagnoses', 'value' => Diagnosis::query()->count(), 'tone' => 'secondary'],
            'bookings' => ['label' => 'Total Bookings', 'value' => Booking::query()->count(), 'tone' => 'primary'],
            'pending_bookings' => ['label' => 'Pending Bookings', 'value' => Booking::query()->where('status', BookingStatus::Pending->value)->count(), 'tone' => 'warning'],
            'sos_requests' => ['label' => 'Total SOS Requests', 'value' => SosRequest::query()->count(), 'tone' => 'danger'],
            'active_subscriptions' => ['label' => 'Active Subscriptions', 'value' => Subscription::query()->where('status', SubscriptionStatus::Active->value)->count(), 'tone' => 'success'],
            'monthly_revenue' => ['label' => 'Monthly Revenue', 'value' => number_format((float) $monthlyRevenue, 2) . ' EGP', 'tone' => 'success'],
            'leads' => ['label' => 'Total Leads', 'value' => Lead::query()->count(), 'tone' => 'info'],
            'reviews' => ['label' => 'Total Reviews', 'value' => Review::query()->count(), 'tone' => 'primary'],
        ];
    }
}
