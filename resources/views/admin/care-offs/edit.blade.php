@extends('layouts.app')

@section('title', 'Edit Care Off')
@section('page-title', 'Edit Care Off')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Care Off</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.care-offs.update', $careOff) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.care-offs._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Update Care Off</button>
                    <a href="{{ route('admin.care-offs.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
