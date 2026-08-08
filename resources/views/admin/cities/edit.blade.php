@extends('layouts.app')

@section('title', 'Edit City')
@section('page-title', 'Edit City')

@section('content')
    <x-admin.form-page
        title="Edit City"
        :action="route('admin.cities.update', $city)"
        method="PUT"
        :cancel-url="route('admin.cities.index')"
        submit-label="Update City"
    >
        @include('admin.cities._form')
    </x-admin.form-page>
@endsection
