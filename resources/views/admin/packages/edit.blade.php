@extends('layouts.app')

@section('title', 'Edit Package')
@section('page-title', 'Edit Package')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Package</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.packages.update', $package) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.packages._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Update Package</button>
                    <a href="{{ route('admin.packages.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
