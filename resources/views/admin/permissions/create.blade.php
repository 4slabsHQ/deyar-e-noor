@extends('layouts.app')

@section('title', 'Create Permission')
@section('page-title', 'Create Permission')

@section('content')
    <x-admin.form-page
        title="Create Permission"
        :action="route('admin.permissions.store')"
        :cancel-url="route('admin.permissions.index')"
        submit-label="Create Permission"
    >
        @include('admin.permissions._form')
    </x-admin.form-page>
@endsection
