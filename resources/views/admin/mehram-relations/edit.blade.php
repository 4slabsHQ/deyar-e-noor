@extends('layouts.app')

@section('title', 'Edit Mehram Relation')
@section('page-title', 'Edit Mehram Relation')

@section('content')
    <x-admin.form-page
        title="Edit Mehram Relation"
        :action="route('admin.mehram-relations.update', $mehramRelation)"
        method="PUT"
        :cancel-url="route('admin.mehram-relations.index')"
        submit-label="Update Mehram Relation"
    >
        @include('admin.mehram-relations._form')
    </x-admin.form-page>
@endsection
