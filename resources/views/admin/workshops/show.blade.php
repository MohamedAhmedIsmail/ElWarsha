@extends('admin.layouts.app')

@section('title', 'Workshop Details')
@section('page_title', 'Workshop Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">{{ $workshop->name }}</h1>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('admin.workshops.edit', $workshop) }}">Edit</a>
            <form method="POST" action="{{ route('admin.workshops.approve', $workshop) }}">@csrf<button class="btn btn-success">Approve</button></form>
            <form method="POST" action="{{ route('admin.workshops.reject', $workshop) }}">@csrf<button class="btn btn-outline-danger">Reject</button></form>
            <form method="POST" action="{{ route('admin.workshops.suspend', $workshop) }}">@csrf<button class="btn btn-warning">Suspend</button></form>
            <form method="POST" action="{{ $workshop->is_verified ? route('admin.workshops.unverify', $workshop) : route('admin.workshops.verify', $workshop) }}">@csrf<button class="btn btn-outline-secondary">{{ $workshop->is_verified ? 'Unverify' : 'Verify' }}</button></form>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6"><div class="card table-card h-100"><div class="card-body">
            <h2 class="h5">Profile</h2>
            <dl class="row mb-0">
                <dt class="col-sm-4">Owner</dt><dd class="col-sm-8">{{ $workshop->owner?->name ?? 'Unassigned' }}</dd>
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8">{{ $workshop->status?->value ?? $workshop->status }}</dd>
                <dt class="col-sm-4">Verified</dt><dd class="col-sm-8">{{ $workshop->is_verified ? 'Yes' : 'No' }}</dd>
                <dt class="col-sm-4">Location</dt><dd class="col-sm-8">{{ $workshop->city }} / {{ $workshop->area }}<br>{{ $workshop->address }}</dd>
                <dt class="col-sm-4">Contact</dt><dd class="col-sm-8">{{ $workshop->phone }}<br>{{ $workshop->email }}</dd>
                <dt class="col-sm-4">Subscription</dt><dd class="col-sm-8">{{ $workshop->subscription_status }}</dd>
            </dl>
        </div></div></div>
        <div class="col-lg-6"><div class="card table-card h-100"><div class="card-body">
            <h2 class="h5">Services & Brands</h2>
            <p><strong>Services:</strong> {{ $workshop->services->pluck('name')->join(', ') ?: 'None' }}</p>
            <p><strong>Brands:</strong> {{ $workshop->brands->pluck('name')->join(', ') ?: 'None' }}</p>
            <p class="mb-0"><strong>Rating:</strong> {{ $workshop->rating_avg }} ({{ $workshop->reviews_count }} reviews)</p>
        </div></div></div>
        <div class="col-lg-6"><div class="card table-card h-100"><div class="card-body"><h2 class="h5">Working Hours</h2><table class="table mb-0"><tbody>@foreach ($workshop->workingHours as $hour)<tr><td class="text-capitalize">{{ $hour->day_of_week?->value ?? $hour->day_of_week }}</td><td>{{ $hour->is_closed ? 'Closed' : (($hour->opens_at ?: '-') . ' - ' . ($hour->closes_at ?: '-')) }}</td></tr>@endforeach</tbody></table></div></div></div>
        <div class="col-lg-6"><div class="card table-card h-100"><div class="card-body"><h2 class="h5">Images</h2><ul class="list-group list-group-flush">@forelse ($workshop->images as $image)<li class="list-group-item px-0">{{ $image->image_path }} <span class="badge text-bg-secondary">{{ $image->type?->value ?? $image->type }}</span></li>@empty<li class="list-group-item px-0 text-muted">No images.</li>@endforelse</ul></div></div></div>
        <div class="col-12"><div class="card table-card"><div class="card-body"><h2 class="h5">Verifications</h2><div class="table-responsive"><table class="table mb-0"><thead><tr><th>ID</th><th>Status</th><th>Verified By</th><th>Verified At</th><th></th></tr></thead><tbody>@forelse ($workshop->verifications as $verification)<tr><td>#{{ $verification->id }}</td><td>{{ $verification->status }}</td><td>{{ $verification->verifier?->name }}</td><td>{{ $verification->verified_at }}</td><td class="text-end"><a href="{{ route('admin.workshop-verifications.show', $verification) }}" class="btn btn-sm btn-outline-secondary">Show</a></td></tr>@empty<tr><td colspan="5" class="text-muted text-center py-3">No verification records.</td></tr>@endforelse</tbody></table></div></div></div></div>
    </div>
@endsection
