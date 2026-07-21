@extends('admin.layouts.app')

@section('title', 'Verification Details')
@section('page_title', 'Verification Details')

@section('content')
    <div class="row g-3">
        <div class="col-lg-7"><div class="card table-card h-100"><div class="card-body">
            <h1 class="h4">{{ $verification->workshop?->name }}</h1>
            <dl class="row">
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8">{{ $verification->status }}</dd>
                <dt class="col-sm-4">Verified By</dt><dd class="col-sm-8">{{ $verification->verifier?->name ?? '-' }}</dd>
                <dt class="col-sm-4">Verified At</dt><dd class="col-sm-8">{{ $verification->verified_at?->format('Y-m-d H:i') ?? '-' }}</dd>
                <dt class="col-sm-4">Admin Notes</dt><dd class="col-sm-8">{{ $verification->admin_notes ?: '-' }}</dd>
            </dl>
            <h2 class="h5">Documents</h2>
            <div class="list-group">
                @foreach ([
                    'commercial_register' => 'Commercial Register',
                    'tax_card' => 'Tax Card',
                    'owner_id_image' => 'Owner ID Image',
                    'workshop_license' => 'Workshop License',
                ] as $field => $label)
                    <div class="list-group-item d-flex justify-content-between">
                        <span>{{ $label }}</span>
                        @if ($verification->{$field})
                            <a href="{{ asset('storage/' . $verification->{$field}) }}" target="_blank">{{ $verification->{$field} }}</a>
                        @else
                            <span class="text-muted">Not uploaded</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div></div></div>
        <div class="col-lg-5"><div class="card table-card"><div class="card-body">
            <h2 class="h5">Decision</h2>
            <form method="POST" action="{{ route('admin.workshop-verifications.approve', $verification) }}" class="mb-3">
                @csrf
                <label class="form-label">Admin Notes</label>
                <textarea class="form-control mb-2" name="admin_notes" rows="4">{{ old('admin_notes', $verification->admin_notes) }}</textarea>
                <button class="btn btn-success w-100">Approve Verification</button>
            </form>
            <form method="POST" action="{{ route('admin.workshop-verifications.reject', $verification) }}">
                @csrf
                <label class="form-label">Rejection Notes</label>
                <textarea class="form-control mb-2" name="admin_notes" rows="4">{{ old('admin_notes', $verification->admin_notes) }}</textarea>
                <button class="btn btn-outline-danger w-100">Reject Verification</button>
            </form>
        </div></div></div>
    </div>
@endsection
