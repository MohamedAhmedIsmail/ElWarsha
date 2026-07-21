@extends('admin.layouts.app')
@section('title', 'Create Car Brand')
@section('page_title', 'Create Car Brand')
@section('content')
<div class="card table-card"><div class="card-body"><form method="POST" action="{{ route('admin.car-brands.store') }}" enctype="multipart/form-data">@include('admin.car-brands._form')</form></div></div>
@endsection
