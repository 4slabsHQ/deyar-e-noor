@extends('layouts.app')

@section('title', 'Edit Tax')
@section('page-title', 'Edit Tax')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Tax</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.taxes.update', $tax) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.taxes._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Update Tax</button>
                    <a href="{{ route('admin.taxes.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection