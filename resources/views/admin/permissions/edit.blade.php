@extends('layouts.app')

@section('title', 'Edit Permission')
@section('page-title', 'Edit Permission')

@section('content')
    <x-admin.form-page
        title="Edit Permission"
        :action="route('admin.permissions.update', $permission)"
        method="PUT"
        :cancel-url="route('admin.permissions.index')"
        submit-label="Update Permission"
    >
        @include('admin.permissions._form', ['permission' => $permission])
    </x-admin.form-page>
@endsection
