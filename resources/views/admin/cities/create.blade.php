@extends('layouts.app')

@section('title', 'Create City')
@section('page-title', 'Create City')

@section('content')
    <x-admin.form-page
        title="Create City"
        :action="route('admin.cities.store')"
        :cancel-url="route('admin.cities.index')"
        submit-label="Create City"
    >
        @include('admin.cities._form')
    </x-admin.form-page>
@endsection
