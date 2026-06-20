@extends('layouts.app')

@section('title', 'Edit Currency')
@section('page-title', 'Edit Currency')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Currency</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.currencies.update', $currency) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.currencies._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Update Currency</button>
                    <a href="{{ route('admin.currencies.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection