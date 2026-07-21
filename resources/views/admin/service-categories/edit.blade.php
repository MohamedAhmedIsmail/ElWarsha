@extends('admin.layouts.app')
@section('title', 'Edit Service Category')
@section('page_title', 'Edit Service Category')
@section('content')
<div class="card table-card"><div class="card-body"><form method="POST" action="{{ route('admin.service-categories.update', $category) }}">@include('admin.service-categories._form')</form></div></div>
@endsection
