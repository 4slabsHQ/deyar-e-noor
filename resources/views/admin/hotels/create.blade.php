@extends('layouts.app')

@section('title', 'Create Hotel')
@section('page-title', 'Create Hotel')

@section('content')
    <x-admin.form-page
        title="Create Hotel"
        :action="route('admin.hotels.store')"
        :cancel-url="route('admin.hotels.index')"
        submit-label="Create Hotel"
    >
        @include('admin.hotels._form')
    </x-admin.form-page>
@endsection
