@extends('layouts.app')

@section('title', 'Create User')
@section('page-title', 'Create User')

@section('content')
    <x-admin.form-page
        title="Create New User"
        :action="route('admin.users.store')"
        :cancel-url="route('admin.users.index')"
        submit-label="Create User"
        enctype="multipart/form-data"
    >
        @include('admin.users._form')
    </x-admin.form-page>
@endsection
