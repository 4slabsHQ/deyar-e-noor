@extends('layouts.app')

@section('title', 'Create Maktab Category')
@section('page-title', 'Create Maktab Category')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Maktab Category</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.maktab-categories.store') }}" method="POST">
            @csrf
            @include('admin.maktab-categories._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Create Maktab Category</button>
                    <a href="{{ route('admin.maktab-categories.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
