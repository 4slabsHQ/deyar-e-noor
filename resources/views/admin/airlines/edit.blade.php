@extends('layouts.app')

@section('title', 'Edit Airline')
@section('page-title', 'Edit Airline')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Airline</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.airlines.update', $airline) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.airlines._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Update Airline</button>
                    <a href="{{ route('admin.airlines.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection