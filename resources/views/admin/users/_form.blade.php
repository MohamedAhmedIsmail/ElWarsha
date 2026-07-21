@csrf
@if ($user->exists)
    @method('PUT')
@endif

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Name</label>
        <input class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Phone</label>
        <input class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $user->phone) }}" required>
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Password {{ $user->exists ? '(leave blank to keep)' : '' }}</label>
        <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" {{ $user->exists ? '' : 'required' }}>
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Role</label>
        <select class="form-select" name="role" required>
            @foreach ($roles as $role)
                <option value="{{ $role->value }}" @selected(old('role', $user->role?->value ?? $user->role) === $role->value)>{{ $role->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select class="form-select" name="status" required>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $user->status?->value ?? $user->status) === $status->value)>{{ $status->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">City</label>
        <input class="form-control" name="city" value="{{ old('city', $user->city) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Area</label>
        <input class="form-control" name="area" value="{{ old('area', $user->area) }}">
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary">{{ $user->exists ? 'Update' : 'Create' }}</button>
    <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Cancel</a>
</div>
