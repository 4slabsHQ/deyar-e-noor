@extends('layouts.app')

@section('title', 'Edit Tax')
@section('page-title', 'Edit Tax')

@section('content')
    <x-admin.form-page
        title="Edit Tax"
        :action="route('admin.taxes.update', $tax)"
        method="PUT"
        :cancel-url="route('admin.taxes.index')"
        submit-label="Update Tax"
    >
        @include('admin.taxes._form')
    </x-admin.form-page>
@endsection
