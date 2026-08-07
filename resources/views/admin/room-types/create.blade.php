@extends('layouts.app')

@section('title', 'Create Room Type')
@section('page-title', 'Create Room Type')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Room Type</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.room-types.store') }}" method="POST">
            @csrf
            @include('admin.room-types._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Create Room Type</button>
                    <a href="{{ route('admin.room-types.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
