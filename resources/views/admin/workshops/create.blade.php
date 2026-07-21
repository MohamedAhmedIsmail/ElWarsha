@extends('admin.layouts.app')

@section('title', 'Create Workshop')
@section('page_title', 'Create Workshop')

@section('content')
    <div class="card table-card"><div class="card-body">
        <form method="POST" action="{{ route('admin.workshops.store') }}">
            @include('admin.workshops._form')
        </form>
    </div></div>
@endsection
