@extends('layouts.app')

@section('title', 'Create Waris Relation')
@section('page-title', 'Create Waris Relation')

@section('content')
    <x-admin.form-page
        title="Create Waris Relation"
        :action="route('admin.waris-relations.store')"
        :cancel-url="route('admin.waris-relations.index')"
        submit-label="Create Waris Relation"
    >
        @include('admin.waris-relations._form')
    </x-admin.form-page>
@endsection
