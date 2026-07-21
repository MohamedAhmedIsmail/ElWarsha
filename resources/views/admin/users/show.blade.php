@extends('admin.layouts.app')

@section('title', 'User Details')
@section('page_title', 'User Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">{{ $user->name }}</h1>
            <div class="text-muted">{{ $user->phone }} {{ $user->email ? ' - ' . $user->email : '' }}</div>
        </div>
        <a class="btn btn-outline-primary" href="{{ route('admin.users.edit', $user) }}">Edit User</a>
    </div>

    <div class="row g-4">
        @foreach ([
            'Vehicles' => $user->vehicles,
            'Bookings' => $user->bookings,
            'Diagnoses' => $user->diagnoses,
            'SOS Requests' => $user->sosRequests,
            'Reviews' => $user->reviews,
        ] as $title => $items)
            <div class="col-12 col-xl-6">
                <div class="card table-card h-100">
                    <div class="card-header bg-white fw-semibold">{{ $title }} <span class="badge text-bg-secondary">{{ $items->count() }}</span></div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead><tr><th>ID</th><th>Summary</th><th>Status</th></tr></thead>
                            <tbody>
                            @forelse ($items->take(10) as $item)
                                <tr>
                                    <td>#{{ $item->id }}</td>
                                    <td>{{ $item->name ?? $item->description ?? $item->title ?? $item->plate_number ?? 'Record' }}</td>
                                    <td>{{ $item->status?->value ?? $item->status ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted text-center py-3">No records.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
