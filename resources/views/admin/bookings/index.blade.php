@extends('admin.layouts.app')

@section('title', 'Bookings')
@section('page_title', 'Bookings')

@section('content')
    <h1 class="h4 mb-3">Bookings</h1>
    <form class="card table-card mb-3" method="GET">
        <div class="card-body row g-3">
            <div class="col-md-2"><select class="form-select" name="status"><option value="">All statuses</option>@foreach ($statuses as $status)<option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->value }}</option>@endforeach</select></div>
            <div class="col-md-3"><select class="form-select" name="workshop"><option value="">All workshops</option>@foreach ($workshops as $workshop)<option value="{{ $workshop->id }}" @selected((string) request('workshop') === (string) $workshop->id)>{{ $workshop->name }}</option>@endforeach</select></div>
            <div class="col-md-3"><select class="form-select" name="service"><option value="">All services</option>@foreach ($services as $service)<option value="{{ $service->id }}" @selected((string) request('service') === (string) $service->id)>{{ $service->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><input class="form-control" type="date" name="date_from" value="{{ request('date_from') }}"></div>
            <div class="col-md-2"><input class="form-control" type="date" name="date_to" value="{{ request('date_to') }}"></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
        </div>
    </form>
    <div class="card table-card">
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead><tr><th>ID</th><th>User</th><th>Workshop</th><th>Service</th><th>Scheduled</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse ($bookings as $booking)
                <tr>
                    <td>#{{ $booking->id }}</td>
                    <td>{{ $booking->user?->name }}</td>
                    <td>{{ $booking->workshop?->name }}</td>
                    <td>{{ $booking->service?->name ?? '-' }}</td>
                    <td>{{ $booking->scheduled_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td><span class="badge text-bg-secondary">{{ $booking->status?->value ?? $booking->status }}</span></td>
                    <td class="text-end"><a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-secondary">Show</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No bookings found.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        <div class="card-body">{{ $bookings->links() }}</div>
    </div>
@endsection
