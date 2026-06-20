@extends('layouts.app')

@section('title', 'Edit Country')
@section('page-title', 'Edit Country')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Country — {{ $country->name }}</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.countries.update', $country) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.countries._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Update Country</button>
                    <a href="{{ route('admin.countries.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection