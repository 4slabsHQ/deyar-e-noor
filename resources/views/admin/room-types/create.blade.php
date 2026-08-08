@extends('layouts.app')

@section('title', 'Create Room Type')
@section('page-title', 'Create Room Type')

@section('content')
    <x-admin.form-page
        title="Create Room Type"
        :action="route('admin.room-types.store')"
        :cancel-url="route('admin.room-types.index')"
        submit-label="Create Room Type"
    >
        @include('admin.room-types._form')
    </x-admin.form-page>
@endsection
