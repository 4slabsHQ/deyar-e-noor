@extends('layouts.app')

@section('title', 'Create Package')
@section('page-title', 'Create Package')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Package</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.packages.store') }}" method="POST">
            @csrf
            @include('admin.packages._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Create Package</button>
                    <a href="{{ route('admin.packages.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
