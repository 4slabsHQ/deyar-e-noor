@extends('layouts.app')

@section('title', 'Edit Hotel')
@section('page-title', 'Edit Hotel')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Hotel</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.hotels.update', $hotel) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.hotels._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Update Hotel</button>
                    <a href="{{ route('admin.hotels.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection