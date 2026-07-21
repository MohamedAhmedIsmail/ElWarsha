@extends('admin.layouts.app')

@section('title', 'Edit Workshop')
@section('page_title', 'Edit Workshop')

@section('content')
    <div class="card table-card mb-3"><div class="card-body">
        <form method="POST" action="{{ route('admin.workshops.update', $workshop) }}">
            @include('admin.workshops._form')
        </form>
    </div></div>

    <div class="card table-card"><div class="card-body">
        <h2 class="h5">Images</h2>
        <form class="row g-3 mb-3" method="POST" action="{{ route('admin.workshops.images.store', $workshop) }}" enctype="multipart/form-data">
            @csrf
            <div class="col-md-4"><input class="form-control" type="file" name="image" required></div>
            <div class="col-md-3"><select class="form-select" name="type" required>@foreach ($imageTypes as $type)<option value="{{ $type->value }}">{{ $type->value }}</option>@endforeach</select></div>
            <div class="col-md-2"><input class="form-control" type="number" name="sort_order" value="0" min="0"></div>
            <div class="col-md-3"><button class="btn btn-outline-primary">Upload Image</button></div>
        </form>
        <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Path</th><th>Type</th><th>Sort</th><th class="text-end">Action</th></tr></thead><tbody>
            @forelse ($workshop->images as $image)
                <tr><td>{{ $image->image_path }}</td><td>{{ $image->type?->value ?? $image->type }}</td><td>{{ $image->sort_order }}</td><td class="text-end"><form method="POST" action="{{ route('admin.workshop-images.destroy', $image) }}" onsubmit="return confirm('Delete this image?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
            @empty
                <tr><td colspan="4" class="text-muted text-center py-3">No images uploaded.</td></tr>
            @endforelse
        </tbody></table></div>
    </div></div>
@endsection
