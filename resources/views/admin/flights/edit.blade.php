@extends('layouts.app')

@section('title', 'Edit Flight')
@section('page-title', 'Edit Flight')

@section('content')
    <x-admin.form-page
        title="Edit Flight"
        :action="route('admin.flights.update', $flight)"
        method="PUT"
        :cancel-url="route('admin.flights.index')"
        submit-label="Update Flight"
    >
        @include('admin.flights._form', ['flight' => $flight])
    </x-admin.form-page>
@endsection
