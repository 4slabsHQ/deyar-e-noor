@extends('layouts.app')

@section('title', 'Create Airport')
@section('page-title', 'Create Airport')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Airport</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.airports.store') }}" method="POST">
            @csrf
            @include('admin.airports._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Create Airport</button>
                    <a href="{{ route('admin.airports.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
