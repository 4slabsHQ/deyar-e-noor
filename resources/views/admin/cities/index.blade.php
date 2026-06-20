@extends('layouts.app')

@section('title', 'Cities')
@section('page-title', 'Cities')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Cities</h4>
        @can('cities.create')
            <a href="{{ route('admin.cities.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New City
            </a>
        @endcan
    </div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Cities</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No cities yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cities as $city)
                            <tr>
                                <td class="fw-medium">{{ $city->name }}</td>
                                <td>{{ $city->country->name ?? '—' }}</td>
                                <td>
                                    <span class="badge light badge-{{ $city->is_active ? 'success' : 'secondary' }}">
                                        {{ $city->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('cities.update')
                                            <a href="{{ route('admin.cities.edit', $city) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('cities.delete')
                                            <form action="{{ route('admin.cities.destroy', $city) }}" method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this city?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger shadow btn-xs sharp">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection