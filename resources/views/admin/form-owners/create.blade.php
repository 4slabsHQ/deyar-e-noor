@extends('layouts.app')

@section('title', 'Create Form Owner')
@section('page-title', 'Create Form Owner')

@section('content')
    <x-admin.form-page
        title="Create Form Owner"
        :action="route('admin.form-owners.store')"
        :cancel-url="route('admin.form-owners.index')"
        submit-label="Create Form Owner"
    >
        @include('admin.form-owners._form')
    </x-admin.form-page>
@endsection
