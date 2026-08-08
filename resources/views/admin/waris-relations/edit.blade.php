@extends('layouts.app')

@section('title', 'Edit Waris Relation')
@section('page-title', 'Edit Waris Relation')

@section('content')
    <x-admin.form-page
        title="Edit Waris Relation"
        :action="route('admin.waris-relations.update', $warisRelation)"
        method="PUT"
        :cancel-url="route('admin.waris-relations.index')"
        submit-label="Update Waris Relation"
    >
        @include('admin.waris-relations._form')
    </x-admin.form-page>
@endsection
