@extends('layouts.app')

@section('title', 'Create Mehram Relation')
@section('page-title', 'Create Mehram Relation')

@section('content')
    <x-admin.form-page
        title="Create Mehram Relation"
        :action="route('admin.mehram-relations.store')"
        :cancel-url="route('admin.mehram-relations.index')"
        submit-label="Create Mehram Relation"
    >
        @include('admin.mehram-relations._form')
    </x-admin.form-page>
@endsection
