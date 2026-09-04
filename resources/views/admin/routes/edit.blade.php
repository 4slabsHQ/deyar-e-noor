@extends('layouts.app')

@section('title', 'Edit Route')
@section('page-title', 'Edit Route')

@section('content')
    <x-admin.form-page
        title="Edit Route"
        :action="route('admin.routes.update', $route)"
        method="PUT"
        :cancel-url="route('admin.routes.index')"
        submit-label="Update Route"
    >
        @include('admin.routes._form', ['route' => $route])
    </x-admin.form-page>
@endsection
