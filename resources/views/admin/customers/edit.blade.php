@extends('layouts.app')

@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Customer</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.customers._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Update Customer</button>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection