@extends('layouts.app')

@section('title', 'Edit Accommodation Plan')
@section('page-title', 'Edit Accommodation Plan')

@section('content')
    <x-admin.form-page
        title="Edit Accommodation Plan"
        :action="route('admin.accommodation-plans.update', $accommodationPlan)"
        method="PUT"
        :cancel-url="route('admin.accommodation-plans.index')"
        submit-label="Update Plan"
    >
        @include('admin.accommodation-plans._form', ['accommodationPlan' => $accommodationPlan])
    </x-admin.form-page>
@endsection
