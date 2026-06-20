@extends('layouts.app')

@section('title', 'Create Country')
@section('page-title', 'Create Country')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Country</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.countries.store') }}" method="POST">
            @csrf
            @include('admin.countries._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Create Country</button>
                    <a href="{{ route('admin.countries.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection