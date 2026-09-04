@extends('layouts.app')

@section('title', 'Create Accommodation Plan')
@section('page-title', 'Create Accommodation Plan')

@section('content')
    <x-admin.form-page
        title="Create Accommodation Plan"
        :action="route('admin.accommodation-plans.store')"
        :cancel-url="route('admin.accommodation-plans.index')"
        submit-label="Create Plan"
    >
        @include('admin.accommodation-plans._form')
    </x-admin.form-page>
@endsection
