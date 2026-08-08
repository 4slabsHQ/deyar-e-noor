@extends('layouts.app')

@section('title', 'Create Country')
@section('page-title', 'Create Country')

@section('content')
    <x-admin.form-page
        title="Create Country"
        :action="route('admin.countries.store')"
        :cancel-url="route('admin.countries.index')"
        submit-label="Create Country"
    >
        @include('admin.countries._form')
    </x-admin.form-page>
@endsection
