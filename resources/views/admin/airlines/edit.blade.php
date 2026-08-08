@extends('layouts.app')

@section('title', 'Edit Airline')
@section('page-title', 'Edit Airline')

@section('content')
    <x-admin.form-page
        title="Edit Airline"
        :action="route('admin.airlines.update', $airline)"
        method="PUT"
        :cancel-url="route('admin.airlines.index')"
        submit-label="Update Airline"
        enctype="multipart/form-data"
    >
        @include('admin.airlines._form')
    </x-admin.form-page>
@endsection
