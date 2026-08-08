@extends('layouts.app')

@section('title', 'Create Tax')
@section('page-title', 'Create Tax')

@section('content')
    <x-admin.form-page
        title="Create Tax"
        :action="route('admin.taxes.store')"
        :cancel-url="route('admin.taxes.index')"
        submit-label="Create Tax"
    >
        @include('admin.taxes._form')
    </x-admin.form-page>
@endsection
