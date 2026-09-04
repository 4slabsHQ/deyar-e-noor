@extends('layouts.app')

@section('title', 'Create Property')
@section('page-title', 'Create Property')

@section('content')
    <x-admin.form-page
        title="Create Property"
        :action="route('admin.properties.store')"
        :cancel-url="route('admin.properties.index')"
        submit-label="Create Property"
    >
        @include('admin.properties._form')
    </x-admin.form-page>
@endsection
