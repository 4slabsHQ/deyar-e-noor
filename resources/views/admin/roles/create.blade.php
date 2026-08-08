@extends('layouts.app')

@section('title', 'New Role')
@section('page-title', 'New Role')

@section('content')
    <x-admin.form-page
        title="Create Role"
        :action="route('admin.roles.store')"
        :cancel-url="route('admin.roles.index')"
        submit-label="Save Role"
    >
        @include('admin.roles._form')
    </x-admin.form-page>
@endsection
