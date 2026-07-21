@extends('admin.layouts.app')

@section('title', 'Create User')
@section('page_title', 'Create User')

@section('content')
    <div class="card table-card"><div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @include('admin.users._form')
        </form>
    </div></div>
@endsection
