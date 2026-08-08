@extends('layouts.app')

@section('title', 'Create Care Off')
@section('page-title', 'Create Care Off')

@section('content')
    <x-admin.form-page
        title="Create Care Off"
        :action="route('admin.care-offs.store')"
        :cancel-url="route('admin.care-offs.index')"
        submit-label="Create Care Off"
    >
        @include('admin.care-offs._form')
    </x-admin.form-page>
@endsection
