@extends('admin.layouts.app')
@section('title', 'Edit Car Model')
@section('page_title', 'Edit Car Model')
@section('content')
<div class="card table-card"><div class="card-body"><form method="POST" action="{{ route('admin.car-models.update', $model) }}">@include('admin.car-models._form')</form></div></div>
@endsection
