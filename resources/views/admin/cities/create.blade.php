@extends('layouts.app')

@section('title', 'Create City')
@section('page-title', 'Create City')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Create City</h4>
    </div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">City Details</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.cities.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-4 form-group">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="country_id" class="form-label">Country</label>
                        <select class="form-control" id="country_id" name="country_id" required>
                            <option value="">Select a country</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="is_active" class="form-label">Status</label>
                        <select class="form-control" id="is_active" name="is_active" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Create City</button>
            </form>
        </div>
    </div>
@endsection