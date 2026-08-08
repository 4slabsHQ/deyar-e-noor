@extends('layouts.app')

@section('title', 'Create Airport')
@section('page-title', 'Create Airport')

@section('content')
    <x-admin.form-page
        title="Create Airport"
        :action="route('admin.airports.store')"
        :cancel-url="route('admin.airports.index')"
        submit-label="Create Airport"
    >
        @include('admin.airports._form')
    </x-admin.form-page>
@endsection
