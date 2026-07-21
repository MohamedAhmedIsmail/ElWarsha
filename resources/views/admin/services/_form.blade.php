@csrf
@if ($service->exists) @method('PUT') @endif
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Category</label><select class="form-select" name="service_category_id" required>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(old('service_category_id', $service->service_category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
    <div class="col-md-6"><label class="form-label">Name</label><input class="form-control" name="name" value="{{ old('name', $service->name) }}" required></div>
    <div class="col-md-6"><label class="form-label">Slug</label><input class="form-control" name="slug" value="{{ old('slug', $service->slug) }}" placeholder="auto-generated when blank"></div>
    <div class="col-md-6"><label class="form-label">Status</label><select class="form-select" name="status" required>@foreach ($statuses as $status)<option value="{{ $status->value }}" @selected(old('status', $service->status?->value ?? $service->status) === $status->value)>{{ $status->value }}</option>@endforeach</select></div>
    <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="4">{{ old('description', $service->description) }}</textarea></div>
</div>
<div class="mt-4 d-flex gap-2"><button class="btn btn-primary">{{ $service->exists ? 'Update' : 'Create' }}</button><a class="btn btn-outline-secondary" href="{{ route('admin.services.index') }}">Cancel</a></div>
