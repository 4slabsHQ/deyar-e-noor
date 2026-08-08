@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
    <x-admin.form-page
        title="Assign Role — {{ $user->name }}"
        :action="route('admin.users.update', $user)"
        method="PUT"
        :cancel-url="route('admin.users.index')"
        submit-label="Update Role"
    >
        @include('admin.users._form', ['user' => $user])
    </x-admin.form-page>
@endsection
