@extends('layouts.app')

@section('title', 'Edit Supplier')
@section('page-title', 'Edit Supplier')

@section('content')
    <x-admin.form-page
        title="Edit Supplier"
        :action="route('admin.suppliers.update', $supplier)"
        method="PUT"
        :cancel-url="route('admin.suppliers.index')"
        submit-label="Update Supplier"
    >
        @include('admin.suppliers._form')
    </x-admin.form-page>
@endsection
