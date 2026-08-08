@extends('layouts.app')

@section('title', 'Edit Airport')
@section('page-title', 'Edit Airport')

@section('content')
    <x-admin.form-page
        title="Edit Airport"
        :action="route('admin.airports.update', $airport)"
        method="PUT"
        :cancel-url="route('admin.airports.index')"
        submit-label="Update Airport"
    >
        @include('admin.airports._form')
    </x-admin.form-page>
@endsection
