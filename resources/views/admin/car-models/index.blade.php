@extends('admin.layouts.app')

@section('title', 'Car Models')
@section('page_title', 'Car Models')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h4 mb-0">Car Models</h1><a href="{{ route('admin.car-models.create') }}" class="btn btn-primary">Create Model</a></div>
    <form class="card table-card mb-3" method="GET"><div class="card-body row g-3"><div class="col-md-10"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by name"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100">Search</button></div></div></form>
    <div class="card table-card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead><tr><th>ID</th><th>Brand</th><th>Name</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @forelse ($models as $model)
            <tr><td>#{{ $model->id }}</td><td>{{ $model->brand?->name }}</td><td>{{ $model->name }}</td><td><span class="badge text-bg-{{ ($model->status?->value ?? $model->status) === 'active' ? 'success' : 'secondary' }}">{{ $model->status?->value ?? $model->status }}</span></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.car-models.edit', $model) }}">Edit</a> <form class="d-inline" method="POST" action="{{ route('admin.car-models.destroy', $model) }}" onsubmit="return confirm('Delete this model?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
        @empty
            <tr><td colspan="5" class="text-muted text-center py-4">No models found.</td></tr>
        @endforelse
        </tbody>
    </table></div><div class="card-body">{{ $models->links() }}</div></div>
@endsection
