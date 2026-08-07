@extends('layouts.app')

@section('title', 'Create Waris Relation')
@section('page-title', 'Create Waris Relation')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Waris Relation</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.waris-relations.store') }}" method="POST">
            @csrf
            @include('admin.waris-relations._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Create Waris Relation</button>
                    <a href="{{ route('admin.waris-relations.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
