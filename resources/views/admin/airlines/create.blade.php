@extends('layouts.app')

@section('title', 'Create Airline')
@section('page-title', 'Create Airline')

@section('content')
    <x-admin.form-page
        title="Create Airline"
        :action="route('admin.airlines.store')"
        :cancel-url="route('admin.airlines.index')"
        submit-label="Create Airline"
        enctype="multipart/form-data"
    >
        @include('admin.airlines._form')
    </x-admin.form-page>
@endsection
