@extends('admin.layouts.app')

@section('title', 'Car Brands')
@section('page_title', 'Car Brands')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Car Brands</h1>
        <a href="{{ route('admin.car-brands.create') }}" class="btn btn-primary">Create Brand</a>
    </div>
    <form class="card table-card mb-3" method="GET"><div class="card-body row g-3">
        <div class="col-md-10"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by name"></div>
        <div class="col-md-2"><button class="btn btn-outline-primary w-100">Search</button></div>
    </div></form>
    <div class="card table-card"><div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>ID</th><th>Logo</th><th>Name</th><th>Models</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse ($brands as $brand)
                <tr>
                    <td>#{{ $brand->id }}</td>
                    <td>{{ $brand->logo ? basename($brand->logo) : '-' }}</td>
                    <td>{{ $brand->name }}</td>
                    <td>{{ $brand->models_count }}</td>
                    <td><span class="badge text-bg-{{ ($brand->status?->value ?? $brand->status) === 'active' ? 'success' : 'secondary' }}">{{ $brand->status?->value ?? $brand->status }}</span></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.car-brands.edit', $brand) }}">Edit</a>
                        <form class="d-inline" method="POST" action="{{ route('admin.car-brands.destroy', $brand) }}" onsubmit="return confirm('Delete this brand and its models?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted text-center py-4">No brands found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div><div class="card-body">{{ $brands->links() }}</div></div>
@endsection
