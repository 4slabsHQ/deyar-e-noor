@extends('layouts.app')

@section('title', 'Edit Care Off')
@section('page-title', 'Edit Care Off')

@section('content')
    <x-admin.form-page
        title="Edit Care Off"
        :action="route('admin.care-offs.update', $careOff)"
        method="PUT"
        :cancel-url="route('admin.care-offs.index')"
        submit-label="Update Care Off"
    >
        @include('admin.care-offs._form')
    </x-admin.form-page>
@endsection
