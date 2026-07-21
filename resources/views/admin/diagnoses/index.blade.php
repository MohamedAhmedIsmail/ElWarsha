@extends('admin.layouts.app')

@section('title', 'Diagnoses')
@section('page_title', 'Diagnoses')

@section('content')
    <h1 class="h4 mb-3">Diagnoses</h1>

    <form class="card table-card mb-3" method="GET">
        <div class="card-body row g-3">
            <div class="col-md-3"><select class="form-select" name="status"><option value="">All statuses</option>@foreach ($statuses as $status)<option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->value }}</option>@endforeach</select></div>
            <div class="col-md-3"><select class="form-select" name="urgency"><option value="">All urgency</option>@foreach ($urgencies as $urgency)<option value="{{ $urgency->value }}" @selected(request('urgency') === $urgency->value)>{{ $urgency->value }}</option>@endforeach</select></div>
            <div class="col-md-3"><select class="form-select" name="confidence"><option value="">All confidence</option>@foreach ($confidences as $confidence)<option value="{{ $confidence->value }}" @selected(request('confidence') === $confidence->value)>{{ $confidence->value }}</option>@endforeach</select></div>
            <div class="col-md-3"><select class="form-select" name="affected_category"><option value="">All categories</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected((string) request('affected_category') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
        </div>
    </form>

    <div class="card table-card">
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead><tr><th>ID</th><th>User</th><th>Vehicle</th><th>Category</th><th>Status</th><th>Urgency</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse ($diagnoses as $diagnosis)
                <tr>
                    <td>#{{ $diagnosis->id }}</td>
                    <td>{{ $diagnosis->user?->name }}</td>
                    <td>{{ $diagnosis->vehicle?->brand?->name }} {{ $diagnosis->vehicle?->model?->name }}</td>
                    <td>{{ $diagnosis->affectedCategory?->name ?? '-' }}</td>
                    <td><span class="badge text-bg-secondary">{{ $diagnosis->status?->value ?? $diagnosis->status }}</span></td>
                    <td>{{ $diagnosis->urgency?->value ?? $diagnosis->urgency }}</td>
                    <td class="text-end"><a href="{{ route('admin.diagnoses.show', $diagnosis) }}" class="btn btn-sm btn-outline-secondary">Show</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No diagnoses found.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        <div class="card-body">{{ $diagnoses->links() }}</div>
    </div>
@endsection
