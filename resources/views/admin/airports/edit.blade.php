@extends('layouts.app')

@section('title', 'Edit Airport')
@section('page-title', 'Edit Airport')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Airport</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.airports.update', $airport) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.airports._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Update Airport</button>
                    <a href="{{ route('admin.airports.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
