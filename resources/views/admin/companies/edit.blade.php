@extends('layouts.app')

@section('title', 'Edit Company')
@section('page-title', 'Edit Company')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Company</h4>
    <a href="{{ route('admin.companies.index') }}" class="btn btn-light">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.companies.update', $company) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.companies._form')
            <div class="text-end mt-3">
                <a href="{{ route('admin.companies.index') }}" class="btn btn-light me-2">Cancel</a>
                <button class="btn btn-primary">Update Company</button>
            </div>
        </form>
    </div>
</div>
@endsection