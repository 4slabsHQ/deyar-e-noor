@extends('layouts.app')

@section('title', 'Edit Company')
@section('page-title', 'Edit Company')

@section('content')
    <x-admin.form-page
        title="Edit Company"
        :action="route('admin.companies.update', $company)"
        method="PUT"
        :cancel-url="route('admin.companies.index')"
        submit-label="Update Company"
        enctype="multipart/form-data"
    >
        @include('admin.companies._form')
    </x-admin.form-page>
@endsection
