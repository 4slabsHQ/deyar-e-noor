@extends('layouts.app')

@section('title', 'Create Customer')
@section('page-title', 'Create Customer')

@section('content')
    <x-admin.form-page
        title="Create Customer"
        :action="route('admin.customers.store')"
        :cancel-url="route('admin.customers.index')"
        submit-label="Create Customer"
    >
        @include('admin.customers._form')
    </x-admin.form-page>
@endsection
