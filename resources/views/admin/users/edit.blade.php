@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Assign Role — {{ $user->name }}</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3 row">
                <label class="col-lg-3 col-form-label">Role</label>
                <div class="col-lg-8">
                    <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                        <option value="">-- Select Role --</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Update Role</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection