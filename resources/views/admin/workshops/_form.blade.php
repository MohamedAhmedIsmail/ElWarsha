@csrf
@if ($workshop->exists) @method('PUT') @endif
@php
    $selectedServices = collect(old('service_ids', $workshop->exists ? $workshop->services->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all();
    $selectedBrands = collect(old('brand_ids', $workshop->exists ? $workshop->brands->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all();
    $hours = $workshop->relationLoaded('workingHours') ? $workshop->workingHours : collect();
@endphp
<div class="row g-3">
    <div class="col-md-4"><label class="form-label">Owner</label><select class="form-select" name="owner_id"><option value="">Unassigned</option>@foreach ($owners as $owner)<option value="{{ $owner->id }}" @selected(old('owner_id', $workshop->owner_id) == $owner->id)>{{ $owner->name }} - {{ $owner->phone }}</option>@endforeach</select></div>
    <div class="col-md-4"><label class="form-label">Name</label><input class="form-control" name="name" value="{{ old('name', $workshop->name) }}" required></div>
    <div class="col-md-4"><label class="form-label">Phone</label><input class="form-control" name="phone" value="{{ old('phone', $workshop->phone) }}" required></div>
    <div class="col-md-4"><label class="form-label">WhatsApp</label><input class="form-control" name="whatsapp" value="{{ old('whatsapp', $workshop->whatsapp) }}"></div>
    <div class="col-md-4"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="{{ old('email', $workshop->email) }}"></div>
    <div class="col-md-4"><label class="form-label">Google Maps URL</label><input class="form-control" name="google_maps_url" value="{{ old('google_maps_url', $workshop->google_maps_url) }}"></div>
    <div class="col-md-4"><label class="form-label">City</label><input class="form-control" name="city" value="{{ old('city', $workshop->city) }}" required></div>
    <div class="col-md-4"><label class="form-label">Area</label><input class="form-control" name="area" value="{{ old('area', $workshop->area) }}" required></div>
    <div class="col-md-4"><label class="form-label">Address</label><input class="form-control" name="address" value="{{ old('address', $workshop->address) }}" required></div>
    <div class="col-md-3"><label class="form-label">Latitude</label><input class="form-control" name="latitude" value="{{ old('latitude', $workshop->latitude) }}" required></div>
    <div class="col-md-3"><label class="form-label">Longitude</label><input class="form-control" name="longitude" value="{{ old('longitude', $workshop->longitude) }}" required></div>
    <div class="col-md-3"><label class="form-label">Status</label><select class="form-select" name="status" required>@foreach ($statuses as $status)<option value="{{ $status->value }}" @selected(old('status', $workshop->status?->value ?? $workshop->status) === $status->value)>{{ $status->value }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label">Subscription</label><select class="form-select" name="subscription_status" required>@foreach (['free', 'active', 'expired', 'cancelled'] as $subscription)<option value="{{ $subscription }}" @selected(old('subscription_status', $workshop->subscription_status ?? 'free') === $subscription)>{{ $subscription }}</option>@endforeach</select></div>
    <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3">{{ old('description', $workshop->description) }}</textarea></div>
    <div class="col-md-4"><label class="form-label">Services</label><select class="form-select" name="service_ids[]" multiple size="8">@foreach ($services as $service)<option value="{{ $service->id }}" @selected(in_array($service->id, $selectedServices, true))>{{ $service->name }}</option>@endforeach</select></div>
    <div class="col-md-4"><label class="form-label">Car Brands</label><select class="form-select" name="brand_ids[]" multiple size="8">@foreach ($brands as $brand)<option value="{{ $brand->id }}" @selected(in_array($brand->id, $selectedBrands, true))>{{ $brand->name }}</option>@endforeach</select></div>
    <div class="col-md-4">
        <label class="form-label">Flags</label>
        <div class="form-check"><input class="form-check-input" type="checkbox" name="accepts_booking" value="1" @checked(old('accepts_booking', $workshop->accepts_booking))><label class="form-check-label">Accepts booking</label></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" name="accepts_sos" value="1" @checked(old('accepts_sos', $workshop->accepts_sos))><label class="form-check-label">Accepts SOS</label></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" name="is_verified" value="1" @checked(old('is_verified', $workshop->is_verified))><label class="form-check-label">Verified</label></div>
    </div>
    <div class="col-12"><h2 class="h5 mt-2">Working Hours</h2></div>
    @foreach ($days as $day)
        @php $hour = $hours->first(fn ($item) => ($item->day_of_week?->value ?? $item->day_of_week) === $day->value); @endphp
        <div class="col-md-3"><label class="form-label text-capitalize">{{ $day->value }}</label><input class="form-control" type="time" name="hours[{{ $day->value }}][opens_at]" value="{{ old("hours.$day->value.opens_at", $hour?->opens_at) }}"></div>
        <div class="col-md-3"><label class="form-label">Closes</label><input class="form-control" type="time" name="hours[{{ $day->value }}][closes_at]" value="{{ old("hours.$day->value.closes_at", $hour?->closes_at) }}"></div>
        <div class="col-md-2 d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="hours[{{ $day->value }}][is_closed]" value="1" @checked(old("hours.$day->value.is_closed", $hour?->is_closed))><label class="form-check-label">Closed</label></div></div>
    @endforeach
</div>
<div class="mt-4 d-flex gap-2"><button class="btn btn-primary">{{ $workshop->exists ? 'Update' : 'Create' }}</button><a class="btn btn-outline-secondary" href="{{ route('admin.workshops.index') }}">Cancel</a></div>
