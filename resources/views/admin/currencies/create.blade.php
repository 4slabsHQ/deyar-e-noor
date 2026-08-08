@extends('layouts.app')

@section('title', 'Create Currency')
@section('page-title', 'Create Currency')

@section('content')
    <x-admin.form-page
        title="Create Currency"
        :action="route('admin.currencies.store')"
        :cancel-url="route('admin.currencies.index')"
        submit-label="Create Currency"
    >
        @include('admin.currencies._form')
    </x-admin.form-page>
@endsection
