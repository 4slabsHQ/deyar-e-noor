@extends('layouts.app')

@section('title', 'Edit Hajj Registration')
@section('page-title', 'Edit Hajj Registration')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Registration — {{ $pilgrim->full_name }}</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.pilgrims.update', $pilgrim) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.pilgrims._form')

            <div class="form-actions d-flex gap-2">
                <button class="btn btn-primary">Update Registration</button>
                <a href="{{ route('admin.pilgrims.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
