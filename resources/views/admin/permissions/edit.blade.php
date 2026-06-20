@extends('layouts.app')

@section('title', 'Edit Permission')
@section('page-title', 'Edit Permission')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Permission</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3 row">
                <label class="col-lg-3 col-form-label">Permission Name</label>
                <div class="col-lg-8">
                    <input type="text" name="name" value="{{ old('name', $permission->name) }}"
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Update</button>
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection