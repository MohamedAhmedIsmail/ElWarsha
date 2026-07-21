@extends('admin.layouts.app')

@section('title', 'Edit User')
@section('page_title', 'Edit User')

@section('content')
    <div class="card table-card"><div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @include('admin.users._form')
        </form>
    </div></div>
@endsection
