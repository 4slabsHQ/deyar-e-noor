@extends('layouts.app')

@section('title', 'Edit Maktab Category')
@section('page-title', 'Edit Maktab Category')

@section('content')
    <x-admin.form-page
        title="Edit Maktab Category"
        :action="route('admin.maktab-categories.update', $maktabCategory)"
        method="PUT"
        :cancel-url="route('admin.maktab-categories.index')"
        submit-label="Update Maktab Category"
    >
        @include('admin.maktab-categories._form')
    </x-admin.form-page>
@endsection
