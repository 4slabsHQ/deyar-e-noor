@extends('layouts.app')

@section('title', 'Create Form Owner')
@section('page-title', 'Create Form Owner')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Form Owner</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.form-owners.store') }}" method="POST">
            @csrf
            @include('admin.form-owners._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Create Form Owner</button>
                    <a href="{{ route('admin.form-owners.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
