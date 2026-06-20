@extends('layouts.app')

@section('title', 'Create Tax')
@section('page-title', 'Create Tax')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Tax</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.taxes.store') }}" method="POST">
            @csrf
            @include('admin.taxes._form')

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Create Tax</button>
                    <a href="{{ route('admin.taxes.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection