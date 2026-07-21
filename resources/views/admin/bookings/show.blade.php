@extends('admin.layouts.app')

@section('title', 'Booking Details')
@section('page_title', 'Booking Details')

@section('content')
    <div class="row g-3">
        <div class="col-lg-7"><div class="card table-card h-100"><div class="card-body">
            <h1 class="h4">Booking #{{ $booking->id }}</h1>
            <dl class="row">
                <dt class="col-sm-4">User</dt><dd class="col-sm-8">{{ $booking->user?->name }}</dd>
                <dt class="col-sm-4">Vehicle</dt><dd class="col-sm-8">{{ $booking->vehicle?->brand?->name }} {{ $booking->vehicle?->model?->name }}</dd>
                <dt class="col-sm-4">Workshop</dt><dd class="col-sm-8">{{ $booking->workshop?->name }}</dd>
                <dt class="col-sm-4">Diagnosis</dt><dd class="col-sm-8">{{ $booking->diagnosis_id ? '#' . $booking->diagnosis_id : '-' }}</dd>
                <dt class="col-sm-4">Service</dt><dd class="col-sm-8">{{ $booking->service?->name ?? '-' }}</dd>
                <dt class="col-sm-4">Scheduled</dt><dd class="col-sm-8">{{ $booking->scheduled_at?->format('Y-m-d H:i') ?? '-' }}</dd>
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8">{{ $booking->status?->value ?? $booking->status }}</dd>
            </dl>
            <p><strong>Description:</strong><br>{{ $booking->description ?: '-' }}</p>
            <p><strong>Workshop Notes:</strong><br>{{ $booking->workshop_notes ?: '-' }}</p>
            <p><strong>Admin Notes:</strong><br>{{ $booking->admin_notes ?: '-' }}</p>
        </div></div></div>
        <div class="col-lg-5"><div class="card table-card"><div class="card-body">
            <h2 class="h5">Admin Actions</h2>
            <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}" class="mb-3">
                @csrf
                <label class="form-label">Cancel Notes</label>
                <textarea class="form-control mb-2" name="admin_notes" rows="3">{{ old('admin_notes', $booking->admin_notes) }}</textarea>
                <button class="btn btn-outline-danger w-100">Cancel Booking</button>
            </form>
            <form method="POST" action="{{ route('admin.bookings.complete', $booking) }}">
                @csrf
                <label class="form-label">Completion Notes</label>
                <textarea class="form-control mb-2" name="admin_notes" rows="3">{{ old('admin_notes', $booking->admin_notes) }}</textarea>
                <button class="btn btn-success w-100">Force Complete</button>
            </form>
        </div></div></div>
        <div class="col-12"><div class="card table-card"><div class="card-body">
            <h2 class="h5">Status Logs</h2>
            <div class="table-responsive"><table class="table mb-0"><thead><tr><th>Old</th><th>New</th><th>Changed By</th><th>Notes</th><th>Created</th></tr></thead><tbody>
                @forelse ($booking->statusLogs as $log)
                    <tr><td>{{ $log->old_status }}</td><td>{{ $log->new_status }}</td><td>{{ $log->changed_by }}</td><td>{{ $log->notes }}</td><td>{{ $log->created_at?->format('Y-m-d H:i') }}</td></tr>
                @empty
                    <tr><td colspan="5" class="text-muted text-center py-3">No status logs.</td></tr>
                @endforelse
            </tbody></table></div>
        </div></div></div>
    </div>
@endsection
