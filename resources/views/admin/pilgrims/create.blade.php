@extends('layouts.app')

@section('title', 'New Hajj Registration')
@section('page-title', 'New Hajj Registration')

@section('content')
    <x-admin.form-page
        title="New Hajj Registration"
        :action="route('admin.pilgrims.store')"
        :cancel-url="route('admin.pilgrims.index')"
        submit-label="Save Registration"
        enctype="multipart/form-data"
    >
        @include('admin.pilgrims._form')
    </x-admin.form-page>
@endsection
