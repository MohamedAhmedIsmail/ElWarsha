@extends('admin.layouts.app')
@section('title', 'Create Service')
@section('page_title', 'Create Service')
@section('content')
<div class="card table-card"><div class="card-body"><form method="POST" action="{{ route('admin.services.store') }}">@include('admin.services._form')</form></div></div>
@endsection
