@extends('layouts.app')

@section('title', 'Edit Form Owner')
@section('page-title', 'Edit Form Owner')

@section('content')
    <x-admin.form-page
        title="Edit Form Owner"
        :action="route('admin.form-owners.update', $formOwner)"
        method="PUT"
        :cancel-url="route('admin.form-owners.index')"
        submit-label="Update Form Owner"
    >
        @include('admin.form-owners._form')
    </x-admin.form-page>
@endsection
