@extends('layouts.app')

@section('title', 'Edit Property')
@section('page-title', 'Edit Property')

@section('content')
    <x-admin.form-page
        title="Edit Property"
        :action="route('admin.properties.update', $property)"
        method="PUT"
        :cancel-url="route('admin.properties.index')"
        submit-label="Update Property"
    >
        @include('admin.properties._form', ['property' => $property])
    </x-admin.form-page>
@endsection
