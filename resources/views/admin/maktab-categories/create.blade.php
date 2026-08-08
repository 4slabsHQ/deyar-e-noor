@extends('layouts.app')

@section('title', 'Create Maktab Category')
@section('page-title', 'Create Maktab Category')

@section('content')
    <x-admin.form-page
        title="Create Maktab Category"
        :action="route('admin.maktab-categories.store')"
        :cancel-url="route('admin.maktab-categories.index')"
        submit-label="Create Maktab Category"
    >
        @include('admin.maktab-categories._form')
    </x-admin.form-page>
@endsection
