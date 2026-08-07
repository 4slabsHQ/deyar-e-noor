@extends('layouts.app')

@section('title', 'New Hajj Registration')
@section('page-title', 'New Hajj Registration')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">New Hajj Registration</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.pilgrims.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.pilgrims._form')

            <div class="form-actions d-flex gap-2">
                <button class="btn btn-primary">Save Registration</button>
                <a href="{{ route('admin.pilgrims.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
