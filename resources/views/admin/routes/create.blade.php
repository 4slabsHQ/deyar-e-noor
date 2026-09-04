@extends('layouts.app')

@section('title', 'Create Route')
@section('page-title', 'Create Route')

@section('content')
    <x-admin.form-page
        title="Create Route"
        :action="route('admin.routes.store')"
        :cancel-url="route('admin.routes.index')"
        submit-label="Create Route"
    >
        @include('admin.routes._form')
    </x-admin.form-page>
@endsection
