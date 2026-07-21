@csrf
@if ($model->exists) @method('PUT') @endif
<div class="row g-3">
    <div class="col-md-4"><label class="form-label">Brand</label><select class="form-select" name="car_brand_id" required>@foreach ($brands as $brand)<option value="{{ $brand->id }}" @selected(old('car_brand_id', $model->car_brand_id) == $brand->id)>{{ $brand->name }}</option>@endforeach</select></div>
    <div class="col-md-4"><label class="form-label">Name</label><input class="form-control" name="name" value="{{ old('name', $model->name) }}" required></div>
    <div class="col-md-4"><label class="form-label">Status</label><select class="form-select" name="status" required>@foreach ($statuses as $status)<option value="{{ $status->value }}" @selected(old('status', $model->status?->value ?? $model->status) === $status->value)>{{ $status->value }}</option>@endforeach</select></div>
</div>
<div class="mt-4 d-flex gap-2"><button class="btn btn-primary">{{ $model->exists ? 'Update' : 'Create' }}</button><a class="btn btn-outline-secondary" href="{{ route('admin.car-models.index') }}">Cancel</a></div>
