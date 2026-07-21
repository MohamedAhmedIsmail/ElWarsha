@extends('admin.layouts.app')
@section('title', 'Edit Service')
@section('page_title', 'Edit Service')
@section('content')
<div class="card table-card"><div class="card-body"><form method="POST" action="{{ route('admin.services.update', $service) }}">@include('admin.services._form')</form></div></div>
@endsection
