@extends('layouts.app')

@section('title', 'Edit Mehram Relation')
@section('page-title', 'Edit Mehram Relation')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Mehram Relation</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.mehram-relations.update', $mehramRelation) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.mehram-relations._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Update Mehram Relation</button>
                    <a href="{{ route('admin.mehram-relations.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
