@extends('layouts.app')

@section('title', 'Edit Country')
@section('page-title', 'Edit Country')

@section('content')
    <x-admin.form-page
        title="Edit Country"
        :action="route('admin.countries.update', $country)"
        method="PUT"
        :cancel-url="route('admin.countries.index')"
        submit-label="Update Country"
    >
        @include('admin.countries._form')
    </x-admin.form-page>
@endsection
