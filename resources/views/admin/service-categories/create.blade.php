@extends('admin.layouts.app')
@section('title', 'Create Service Category')
@section('page_title', 'Create Service Category')
@section('content')
<div class="card table-card"><div class="card-body"><form method="POST" action="{{ route('admin.service-categories.store') }}">@include('admin.service-categories._form')</form></div></div>
@endsection
