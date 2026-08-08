@extends('layouts.app')

@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')

@section('content')
    <x-admin.form-page
        title="Edit Customer"
        :action="route('admin.customers.update', $customer)"
        method="PUT"
        :cancel-url="route('admin.customers.index')"
        submit-label="Update Customer"
    >
        @include('admin.customers._form')
    </x-admin.form-page>
@endsection
