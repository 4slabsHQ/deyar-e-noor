@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
    <div class="mb-3">
        <x-admin.form-page
            title="Edit User — {{ $user->name }}"
            :action="route('admin.users.update', $user)"
            method="PUT"
            :cancel-url="route('admin.users.index')"
            submit-label="Save User"
            enctype="multipart/form-data"
        >
            @include('admin.users._form', ['user' => $user])
        </x-admin.form-page>
    </div>

    <div class="card admin-form-page">
        <div class="card-header">
            <h4 class="card-title">Password</h4>
        </div>
        <div class="card-body">
            @if (session('status') === 'password-updated')
                <div class="alert alert-success py-2 mb-3">Password updated successfully.</div>
            @endif

            <p class="form-hint mb-3">Set a new password if the user cannot sign in. They will use this password on their next login.</p>

            <form method="POST" action="{{ route('admin.users.password.update', $user) }}" class="admin-form">
                @csrf
                @method('PUT')

                <x-admin.validation-alert />

                <x-admin.form-grid>
                    <x-admin.form-field label="New Password" for="password" class="col-lg-4 col-md-6" :required="true">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            autocomplete="new-password"
                            required
                        />
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </x-admin.form-field>

                    <x-admin.form-field label="Confirm Password" for="password_confirmation" class="col-lg-4 col-md-6" :required="true">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            autocomplete="new-password"
                            required
                        />
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </x-admin.form-field>
                </x-admin.form-grid>

                <x-admin.form-actions submit="Update Password" :cancel-url="route('admin.users.index')" />
            </form>
        </div>
    </div>
@endsection
