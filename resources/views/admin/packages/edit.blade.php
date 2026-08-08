@extends('layouts.app')

@section('title', 'Edit Package')
@section('page-title', 'Edit Package')

@section('content')
    <x-admin.form-page
        title="Edit Package"
        :action="route('admin.packages.update', $package)"
        method="PUT"
        :cancel-url="route('admin.packages.index')"
        submit-label="Update Package"
    >
        @include('admin.packages._form')
    </x-admin.form-page>
@endsection
