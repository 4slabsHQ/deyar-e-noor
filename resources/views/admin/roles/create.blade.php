@extends('layouts.app')

@section('title', 'New Role')
@section('page-title', 'New Role')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Role</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf

            <div class="mb-3 row">
                <label class="col-lg-3 col-form-label">Role Name <span class="text-danger">*</span></label>
                <div class="col-lg-8">
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            @include('admin.roles._permissions')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Save Role</button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection