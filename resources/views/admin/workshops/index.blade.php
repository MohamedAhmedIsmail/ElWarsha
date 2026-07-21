@extends('admin.layouts.app')

@section('title', 'Workshops')
@section('page_title', 'Workshops')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Workshops</h1>
        <a href="{{ route('admin.workshops.create') }}" class="btn btn-primary">Create Workshop</a>
    </div>

    <form class="card table-card mb-3" method="GET">
        <div class="card-body row g-3">
            <div class="col-md-2"><select class="form-select" name="status"><option value="">All statuses</option>@foreach ($statuses as $status)<option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->value }}</option>@endforeach</select></div>
            <div class="col-md-2"><input class="form-control" name="city" value="{{ request('city') }}" placeholder="City"></div>
            <div class="col-md-2"><input class="form-control" name="area" value="{{ request('area') }}" placeholder="Area"></div>
            <div class="col-md-2"><select class="form-select" name="is_verified"><option value="">Verified?</option><option value="1" @selected(request('is_verified') === '1')>Verified</option><option value="0" @selected(request('is_verified') === '0')>Unverified</option></select></div>
            <div class="col-md-2"><select class="form-select" name="accepts_booking"><option value="">Booking?</option><option value="1" @selected(request('accepts_booking') === '1')>Yes</option><option value="0" @selected(request('accepts_booking') === '0')>No</option></select></div>
            <div class="col-md-2"><select class="form-select" name="accepts_sos"><option value="">SOS?</option><option value="1" @selected(request('accepts_sos') === '1')>Yes</option><option value="0" @selected(request('accepts_sos') === '0')>No</option></select></div>
            <div class="col-md-3"><select class="form-select" name="subscription_status"><option value="">All subscriptions</option>@foreach (['free', 'active', 'expired', 'cancelled'] as $subscription)<option value="{{ $subscription }}" @selected(request('subscription_status') === $subscription)>{{ $subscription }}</option>@endforeach</select></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
        </div>
    </form>

    <div class="card table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>ID</th><th>Workshop</th><th>Owner</th><th>Location</th><th>Status</th><th>Services</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse ($workshops as $workshop)
                    <tr>
                        <td>#{{ $workshop->id }}</td>
                        <td>{{ $workshop->name }}<div class="small text-muted">{{ $workshop->phone }}</div></td>
                        <td>{{ $workshop->owner?->name ?? 'Unassigned' }}</td>
                        <td>{{ $workshop->city }} / {{ $workshop->area }}</td>
                        <td>
                            <span class="badge text-bg-secondary">{{ $workshop->status?->value ?? $workshop->status }}</span>
                            @if ($workshop->is_verified)<span class="badge text-bg-success">verified</span>@endif
                        </td>
                        <td>{{ $workshop->services_count }} services, {{ $workshop->brands_count }} brands</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.workshops.show', $workshop) }}">Show</a>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.workshops.edit', $workshop) }}">Edit</a>
                            <form class="d-inline" method="POST" action="{{ route('admin.workshops.destroy', $workshop) }}" onsubmit="return confirm('Delete this workshop?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No workshops found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">{{ $workshops->links() }}</div>
    </div>
@endsection
