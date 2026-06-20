@extends('layouts.app')

@section('title', 'Create Hotel')
@section('page-title', 'Create Hotel')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Hotel</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.hotels.store') }}" method="POST">
            @csrf
            @include('admin.hotels._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Create Hotel</button>
                    <a href="{{ route('admin.hotels.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection