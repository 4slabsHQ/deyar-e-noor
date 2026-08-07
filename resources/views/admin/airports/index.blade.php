@extends('layouts.app')

@section('title', 'Airports')
@section('page-title', 'Airports')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Airports</h4>
        @can('airports.create')
            <a href="{{ route('admin.airports.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Airport
            </a>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Airports</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No airports yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>City</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($airports as $airport)
                            <tr>
                                <td class="fw-medium">{{ $airport->name }}</td>
                                <td>{{ $airport->code }}</td>
                                <td>{{ $airport->city->name ?? '—' }}</td>
                                <td>
                                    <span class="badge light badge-{{ $airport->is_active ? 'success' : 'secondary' }}">
                                        {{ $airport->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('airports.update')
                                            <a href="{{ route('admin.airports.edit', $airport) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('airports.delete')
                                            <form action="{{ route('admin.airports.destroy', $airport) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $airport->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger shadow btn-xs sharp">
                                                    <i class="fa fa-trash"></i>
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
