@extends('admin.layouts.app')

@section('title', 'Services')
@section('page_title', 'Services')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h4 mb-0">Services</h1><a href="{{ route('admin.services.create') }}" class="btn btn-primary">Create Service</a></div>
    <form class="card table-card mb-3" method="GET"><div class="card-body row g-3"><div class="col-md-10"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by name"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100">Search</button></div></div></form>
    <div class="card table-card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead><tr><th>ID</th><th>Category</th><th>Name</th><th>Slug</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @forelse ($services as $service)
            <tr><td>#{{ $service->id }}</td><td>{{ $service->category?->name }}</td><td>{{ $service->name }}</td><td>{{ $service->slug }}</td><td><span class="badge text-bg-{{ ($service->status?->value ?? $service->status) === 'active' ? 'success' : 'secondary' }}">{{ $service->status?->value ?? $service->status }}</span></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.services.edit', $service) }}">Edit</a> <form class="d-inline" method="POST" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm('Delete this service?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
        @empty
            <tr><td colspan="6" class="text-muted text-center py-4">No services found.</td></tr>
        @endforelse
        </tbody>
    </table></div><div class="card-body">{{ $services->links() }}</div></div>
@endsection
