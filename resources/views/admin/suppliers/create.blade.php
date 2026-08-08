@extends('layouts.app')

@section('title', 'Create Supplier')
@section('page-title', 'Create Supplier')

@section('content')
    <x-admin.form-page
        title="Create Supplier"
        :action="route('admin.suppliers.store')"
        :cancel-url="route('admin.suppliers.index')"
        submit-label="Create Supplier"
    >
        @include('admin.suppliers._form')
    </x-admin.form-page>
@endsection
