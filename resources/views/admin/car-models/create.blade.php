@extends('admin.layouts.app')
@section('title', 'Create Car Model')
@section('page_title', 'Create Car Model')
@section('content')
<div class="card table-card"><div class="card-body"><form method="POST" action="{{ route('admin.car-models.store') }}">@include('admin.car-models._form')</form></div></div>
@endsection
