@extends('layouts.app')

@section('title', 'Edit Hotel')
@section('page-title', 'Edit Hotel')

@section('content')
    <x-admin.form-page
        title="Edit Hotel"
        :action="route('admin.hotels.update', $hotel)"
        method="PUT"
        :cancel-url="route('admin.hotels.index')"
        submit-label="Update Hotel"
    >
        @include('admin.hotels._form')
    </x-admin.form-page>
@endsection
