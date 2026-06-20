@extends('layouts.app')

@section('title', 'Create User')
@section('page-title', 'Create User')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create New User</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="mb-3 row">
                <label class="col-lg-3 col-form-label">Name</label>
                <div class="col-lg-8">
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-lg-3 col-form-label">Email</label>
                <div class="col-lg-8">
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-lg-3 col-form-label">Password</label>
                <div class="col-lg-8">
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-lg-3 col-form-label">Confirm Password</label>
                <div class="col-lg-8">
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-lg-3 col-form-label">Role</label>
                <div class="col-lg-8">
                    <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                        <option value="">-- Select Role --</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Create User</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection