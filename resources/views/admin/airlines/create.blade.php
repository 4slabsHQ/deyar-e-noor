@extends('layouts.app')

@section('title', 'Create Airline')
@section('page-title', 'Create Airline')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Airline</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.airlines.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.airlines._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Create Airline</button>
                    <a href="{{ route('admin.airlines.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection