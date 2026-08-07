@extends('layouts.app')

@section('title', 'Packages')
@section('page-title', 'Packages')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Packages</h4>
        @can('packages.create')
            <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Package
            </a>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Packages</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No packages yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Days</th>
                            <th>Qurbani</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packages as $package)
                            <tr>
                                <td class="fw-medium">{{ $package->number }}</td>
                                <td>{{ $package->name }}</td>
                                <td>{{ number_format($package->price, 2) }}</td>
                                <td>{{ $package->days }}</td>
                                <td>
                                    @if ($package->qurbani_included)
                                        <span class="badge light badge-success">Yes</span>
                                    @else
                                        <span class="badge light badge-secondary">No</span>
                                    @endif
                                </td>
                                <td>{{ $package->duration->label() }}</td>
                                <td>
                                    <span class="badge light badge-{{ $package->is_active ? 'success' : 'secondary' }}">
                                        {{ $package->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('packages.update')
                                            <a href="{{ route('admin.packages.edit', $package) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('packages.delete')
                                            <form action="{{ route('admin.packages.destroy', $package) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $package->name }}?')">
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
