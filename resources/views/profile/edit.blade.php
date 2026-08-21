@extends('layouts.app')

@section('title', 'Profile Settings')
@section('page-title', 'Profile Settings')

@section('content')
    <div class="mb-3">
        <x-admin.form-page
            title="Account"
            :action="route('admin.profile.update')"
            method="PATCH"
            :cancel-url="route('dashboard')"
            submit-label="Save"
            enctype="multipart/form-data"
        >
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success py-2 mb-3">Profile updated successfully.</div>
            @endif

            @include('profile._form', ['user' => $user])
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

            @if ($errors->updatePassword->any())
                <div class="alert alert-danger py-2 mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->updatePassword->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('user-password.update') }}" class="admin-form">
                @csrf
                @method('PUT')

                <x-admin.form-grid>
                    <x-admin.form-field label="Current Password" for="current_password" class="col-lg-4 col-md-6" :required="true">
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                            autocomplete="current-password"
                            required
                        />
                        @error('current_password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </x-admin.form-field>

                    <x-admin.form-field label="New Password" for="password" class="col-lg-4 col-md-6" :required="true">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                            autocomplete="new-password"
                            required
                        />
                        @error('password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </x-admin.form-field>

                    <x-admin.form-field label="Confirm Password" for="password_confirmation" class="col-lg-4 col-md-6" :required="true">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                            autocomplete="new-password"
                            required
                        />
                        @error('password_confirmation', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </x-admin.form-field>
                </x-admin.form-grid>

                <x-admin.form-actions submit="Update Password" :cancel-url="route('dashboard')" />
            </form>
        </div>
    </div>
@endsection
