@extends('layouts.app')

@section('title', 'Create Care Off')
@section('page-title', 'Create Care Off')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Care Off</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.care-offs.store') }}" method="POST">
            @csrf
            @include('admin.care-offs._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Create Care Off</button>
                    <a href="{{ route('admin.care-offs.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
