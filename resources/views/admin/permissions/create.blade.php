@extends('layouts.app')

@section('title', 'Create Permission')
@section('page-title', 'Create Permission')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Permission</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.permissions.store') }}" method="POST">
            @csrf

            <div class="mb-3 row">
                <label class="col-lg-3 col-form-label">Permission Name</label>
                <div class="col-lg-8">
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="e.g. invoices.export">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted">Use the convention <code>module.action</code> (e.g. <code>bookings.view</code>).</small>
                </div>
            </div>

            <div class="mb-3 row">
                <div class="col-lg-8 offset-lg-3">
                    <button class="btn btn-primary">Create</button>
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection