@extends('layouts.app')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')

@section('content')
    <x-admin.form-page
        title="Edit Role"
        :action="route('admin.roles.update', $role)"
        method="PUT"
        :cancel-url="route('admin.roles.index')"
        submit-label="Update Role"
    >
        @include('admin.roles._form')
    </x-admin.form-page>
@endsection
