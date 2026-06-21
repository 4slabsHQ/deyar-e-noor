@extends('layouts.app')

@section('title', 'Create Customer')
@section('page-title', 'Create Customer')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Customer</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.customers.store') }}" method="POST">
            @csrf
            @include('admin.customers._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Create Customer</button>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection