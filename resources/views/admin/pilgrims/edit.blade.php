@extends('layouts.app')

@section('title', 'Edit Hajj Registration')
@section('page-title', 'Edit Hajj Registration')

@section('content')
    <x-admin.form-page
        title="Edit Registration — {{ $pilgrim->full_name }}"
        :action="route('admin.pilgrims.update', $pilgrim)"
        method="PUT"
        :cancel-url="route('admin.pilgrims.index')"
        submit-label="Update Registration"
        enctype="multipart/form-data"
    >
        @include('admin.pilgrims._form')
    </x-admin.form-page>
@endsection
