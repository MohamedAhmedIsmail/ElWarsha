@extends('admin.layouts.app')

@section('title', 'Workshop Verifications')
@section('page_title', 'Workshop Verifications')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Workshop Verifications</h1>
    </div>

    <div class="card table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>ID</th><th>Workshop</th><th>Status</th><th>Verified By</th><th>Verified At</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse ($verifications as $verification)
                    <tr>
                        <td>#{{ $verification->id }}</td>
                        <td>{{ $verification->workshop?->name }}</td>
                        <td><span class="badge text-bg-secondary">{{ $verification->status }}</span></td>
                        <td>{{ $verification->verifier?->name ?? '-' }}</td>
                        <td>{{ $verification->verified_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.workshop-verifications.show', $verification) }}">Show</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No verification requests found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">{{ $verifications->links() }}</div>
    </div>
@endsection
