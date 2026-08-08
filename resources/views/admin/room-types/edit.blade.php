@extends('layouts.app')

@section('title', 'Edit Room Type')
@section('page-title', 'Edit Room Type')

@section('content')
    <x-admin.form-page
        title="Edit Room Type"
        :action="route('admin.room-types.update', $roomType)"
        method="PUT"
        :cancel-url="route('admin.room-types.index')"
        submit-label="Update Room Type"
    >
        @include('admin.room-types._form')
    </x-admin.form-page>
@endsection
