@extends('admin.layouts.app')

@section('title', 'Service Categories')
@section('page_title', 'Service Categories')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h4 mb-0">Service Categories</h1><a href="{{ route('admin.service-categories.create') }}" class="btn btn-primary">Create Category</a></div>
    <form class="card table-card mb-3" method="GET"><div class="card-body row g-3"><div class="col-md-10"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by name"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100">Search</button></div></div></form>
    <div class="card table-card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead><tr><th>Sort</th><th>Name</th><th>Slug</th><th>Services</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @forelse ($categories as $category)
            <tr><td>{{ $category->sort_order }}</td><td>{{ $category->name }}</td><td>{{ $category->slug }}</td><td>{{ $category->services_count }}</td><td><span class="badge text-bg-{{ ($category->status?->value ?? $category->status) === 'active' ? 'success' : 'secondary' }}">{{ $category->status?->value ?? $category->status }}</span></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.service-categories.edit', $category) }}">Edit</a> <form class="d-inline" method="POST" action="{{ route('admin.service-categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
        @empty
            <tr><td colspan="6" class="text-muted text-center py-4">No categories found.</td></tr>
        @endforelse
        </tbody>
    </table></div><div class="card-body">{{ $categories->links() }}</div></div>
@endsection
