@csrf
@if ($category->exists) @method('PUT') @endif
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Name</label><input class="form-control" name="name" value="{{ old('name', $category->name) }}" required></div>
    <div class="col-md-6"><label class="form-label">Slug</label><input class="form-control" name="slug" value="{{ old('slug', $category->slug) }}" placeholder="auto-generated when blank"></div>
    <div class="col-md-4"><label class="form-label">Icon</label><input class="form-control" name="icon" value="{{ old('icon', $category->icon) }}"></div>
    <div class="col-md-4"><label class="form-label">Sort Order</label><input class="form-control" type="number" min="0" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" required></div>
    <div class="col-md-4"><label class="form-label">Status</label><select class="form-select" name="status" required>@foreach ($statuses as $status)<option value="{{ $status->value }}" @selected(old('status', $category->status?->value ?? $category->status) === $status->value)>{{ $status->value }}</option>@endforeach</select></div>
    <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="4">{{ old('description', $category->description) }}</textarea></div>
</div>
<div class="mt-4 d-flex gap-2"><button class="btn btn-primary">{{ $category->exists ? 'Update' : 'Create' }}</button><a class="btn btn-outline-secondary" href="{{ route('admin.service-categories.index') }}">Cancel</a></div>
