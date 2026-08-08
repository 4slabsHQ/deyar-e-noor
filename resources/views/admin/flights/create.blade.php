@extends('layouts.app')

@section('title', 'Add Flight')
@section('page-title', 'Add Flight')

@section('content')
    <x-admin.form-page
        title="Add Flight"
        :action="route('admin.flights.store')"
        :cancel-url="route('admin.flights.index')"
        submit-label="Save Flight"
    >
        @include('admin.flights._form')
    </x-admin.form-page>
@endsection
