@extends('admin.layouts.app')

@section('title', 'Diagnosis Details')
@section('page_title', 'Diagnosis Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Diagnosis #{{ $diagnosis->id }}</h1>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('admin.diagnoses.manual-review', $diagnosis) }}">@csrf<button class="btn btn-warning">Manual Review</button></form>
            <form method="POST" action="{{ route('admin.diagnoses.complete', $diagnosis) }}">@csrf<button class="btn btn-success">Mark Completed</button></form>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-lg-6"><div class="card table-card h-100"><div class="card-body">
            <h2 class="h5">Request</h2>
            <dl class="row">
                <dt class="col-sm-4">User</dt><dd class="col-sm-8">{{ $diagnosis->user?->name }}</dd>
                <dt class="col-sm-4">Vehicle</dt><dd class="col-sm-8">{{ $diagnosis->vehicle?->brand?->name }} {{ $diagnosis->vehicle?->model?->name }}</dd>
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8">{{ $diagnosis->status?->value ?? $diagnosis->status }}</dd>
                <dt class="col-sm-4">Confidence</dt><dd class="col-sm-8">{{ $diagnosis->confidence?->value ?? $diagnosis->confidence }}</dd>
                <dt class="col-sm-4">Urgency</dt><dd class="col-sm-8">{{ $diagnosis->urgency?->value ?? $diagnosis->urgency }}</dd>
                <dt class="col-sm-4">Category</dt><dd class="col-sm-8">{{ $diagnosis->affectedCategory?->name ?? '-' }}</dd>
            </dl>
            <p><strong>Description:</strong><br>{{ $diagnosis->description }}</p>
            <p><strong>Diagnosis Text:</strong><br>{{ $diagnosis->diagnosis_text ?: '-' }}</p>
        </div></div></div>
        <div class="col-lg-6"><div class="card table-card h-100"><div class="card-body">
            <h2 class="h5">AI Response</h2>
            <pre class="bg-light p-3 rounded small">{{ json_encode($diagnosis->ai_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            <h2 class="h5">Symptoms</h2>
            <pre class="bg-light p-3 rounded small">{{ json_encode($diagnosis->symptoms_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </div></div></div>
        <div class="col-lg-6"><div class="card table-card h-100"><div class="card-body"><h2 class="h5">Media</h2><ul class="list-group list-group-flush">@forelse ($diagnosis->media as $media)<li class="list-group-item px-0">{{ $media->file_path }} <span class="badge text-bg-secondary">{{ $media->media_type?->value ?? $media->media_type }}</span></li>@empty<li class="list-group-item px-0 text-muted">No media.</li>@endforelse</ul></div></div></div>
        <div class="col-lg-6"><div class="card table-card h-100"><div class="card-body"><h2 class="h5">Suggested Workshops</h2><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Workshop</th><th>Score</th><th>Reason</th></tr></thead><tbody>@forelse ($diagnosis->suggestions as $suggestion)<tr><td>{{ $suggestion->workshop?->name }}</td><td>{{ $suggestion->score }}</td><td>{{ $suggestion->reason }}</td></tr>@empty<tr><td colspan="3" class="text-muted text-center py-3">No suggestions.</td></tr>@endforelse</tbody></table></div></div></div></div>
    </div>
@endsection
