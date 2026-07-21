@csrf
@if ($brand->exists) @method('PUT') @endif
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Name</label>
        <input class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $brand->name) }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select class="form-select" name="status" required>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $brand->status?->value ?? $brand->status) === $status->value)>{{ $status->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Logo</label>
        <input class="form-control" type="file" name="logo">
        @if ($brand->logo)<div class="small text-muted mt-1">Current: {{ $brand->logo }}</div>@endif
    </div>
</div>
<div class="mt-4 d-flex gap-2"><button class="btn btn-primary">{{ $brand->exists ? 'Update' : 'Create' }}</button><a class="btn btn-outline-secondary" href="{{ route('admin.car-brands.index') }}">Cancel</a></div>
