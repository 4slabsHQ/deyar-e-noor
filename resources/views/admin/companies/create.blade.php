@extends('layouts.app')

@section('title', 'New Company')
@section('page-title', 'New Company')

@section('content')
    <x-admin.form-page
        title="Create Company"
        :action="route('admin.companies.store')"
        :cancel-url="route('admin.companies.index')"
        submit-label="Save Company"
        enctype="multipart/form-data"
    >
        @include('admin.companies._form')
    </x-admin.form-page>
@endsection
