@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
    <div class="row g-3 mb-4">
        @foreach ($kpis as $kpi)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card kpi-card h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ $kpi['label'] }}</div>
                        <div class="d-flex align-items-end justify-content-between mt-2">
                            <div class="fs-3 fw-bold">{{ $kpi['value'] }}</div>
                            <span class="badge text-bg-{{ $kpi['tone'] }}">&nbsp;</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-6">
            <div class="card table-card">
                <div class="card-header bg-white fw-semibold">Latest Pending Workshops</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr><th>Name</th><th>City</th><th>Area</th><th>Created</th></tr>
                        </thead>
                        <tbody>
                        @forelse ($latestPendingWorkshops as $workshop)
                            <tr>
                                <td>{{ $workshop->name }}</td>
                                <td>{{ $workshop->city }}</td>
                                <td>{{ $workshop->area }}</td>
                                <td>{{ $workshop->created_at?->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted text-center py-4">No pending workshops.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card table-card">
                <div class="card-header bg-white fw-semibold">Latest Bookings</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr><th>ID</th><th>Customer</th><th>Workshop</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                        @forelse ($latestBookings as $booking)
                            <tr>
                                <td>#{{ $booking->id }}</td>
                                <td>{{ $booking->user?->name }}</td>
                                <td>{{ $booking->workshop?->name }}</td>
                                <td><span class="badge text-bg-secondary">{{ $booking->status?->value ?? $booking->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted text-center py-4">No bookings yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card table-card">
                <div class="card-header bg-white fw-semibold">Latest SOS Requests</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr><th>ID</th><th>Customer</th><th>Type</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                        @forelse ($latestSosRequests as $sosRequest)
                            <tr>
                                <td>#{{ $sosRequest->id }}</td>
                                <td>{{ $sosRequest->user?->name }}</td>
                                <td>{{ $sosRequest->serviceType?->name }}</td>
                                <td><span class="badge text-bg-danger">{{ $sosRequest->status?->value ?? $sosRequest->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted text-center py-4">No SOS requests yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card table-card">
                <div class="card-header bg-white fw-semibold">Latest Payments</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr><th>ID</th><th>Workshop</th><th>Plan</th><th>Amount</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                        @forelse ($latestPayments as $payment)
                            <tr>
                                <td>#{{ $payment->id }}</td>
                                <td>{{ $payment->workshop?->name }}</td>
                                <td>{{ $payment->subscription?->plan?->name }}</td>
                                <td>{{ $payment->amount }} EGP</td>
                                <td><span class="badge text-bg-warning">{{ $payment->status?->value ?? $payment->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted text-center py-4">No payments yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card table-card">
                <div class="card-header bg-white fw-semibold">Top Workshops by Leads</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr><th>Workshop</th><th>City</th><th>Area</th><th>Leads</th></tr>
                        </thead>
                        <tbody>
                        @forelse ($topWorkshopsByLeads as $workshop)
                            <tr>
                                <td>{{ $workshop->name }}</td>
                                <td>{{ $workshop->city }}</td>
                                <td>{{ $workshop->area }}</td>
                                <td><span class="badge text-bg-info">{{ $workshop->leads_count }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted text-center py-4">No lead data yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
