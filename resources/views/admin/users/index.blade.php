@extends('admin.layouts.app')

@section('title', 'Users')
@section('page_title', 'Users')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Users</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Create User</a>
    </div>

    <form class="card table-card mb-3" method="GET">
        <div class="card-body row g-3">
            <div class="col-md-4"><input class="form-control" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name, phone, email"></div>
            <div class="col-md-3">
                <select class="form-select" name="role">
                    <option value="">All roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" @selected(($filters['role'] ?? '') === $role->value)>{{ $role->value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
        </div>
    </form>

    <div class="card table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Role</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>#{{ $user->id }}</td>
                        <td>{{ $user->name }}<div class="small text-muted">{{ $user->email }}</div></td>
                        <td>{{ $user->phone }}</td>
                        <td><span class="badge text-bg-secondary">{{ $user->role?->value ?? $user->role }}</span></td>
                        <td><span class="badge text-bg-{{ ($user->status?->value ?? $user->status) === 'blocked' ? 'danger' : 'success' }}">{{ $user->status?->value ?? $user->status }}</span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.users.show', $user) }}">Show</a>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                            <form class="d-inline" method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">{{ $users->links() }}</div>
    </div>
@endsection
