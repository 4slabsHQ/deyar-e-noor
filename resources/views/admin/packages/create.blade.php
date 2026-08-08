@extends('layouts.app')

@section('title', 'Create Package')
@section('page-title', 'Create Package')

@section('content')
    <x-admin.form-page
        title="Create Package"
        :action="route('admin.packages.store')"
        :cancel-url="route('admin.packages.index')"
        submit-label="Create Package"
    >
        @include('admin.packages._form')
    </x-admin.form-page>
@endsection
