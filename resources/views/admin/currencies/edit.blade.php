@extends('layouts.app')

@section('title', 'Edit Currency')
@section('page-title', 'Edit Currency')

@section('content')
    <x-admin.form-page
        title="Edit Currency"
        :action="route('admin.currencies.update', $currency)"
        method="PUT"
        :cancel-url="route('admin.currencies.index')"
        submit-label="Update Currency"
    >
        @include('admin.currencies._form')
    </x-admin.form-page>
@endsection
