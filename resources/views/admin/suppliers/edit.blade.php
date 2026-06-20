@extends('layouts.app')

@section('title', 'Edit Supplier')
@section('page-title', 'Edit Supplier')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Supplier</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.suppliers.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.suppliers._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Update Supplier</button>
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection