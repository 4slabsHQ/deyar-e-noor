@extends('layouts.app')

@section('title', 'Room Types')
@section('page-title', 'Room Types')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Room Types</h4>
        @can('room-types.create')
            <a href="{{ route('admin.room-types.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Room Type
            </a>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Room Types</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No room types yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roomTypes as $roomType)
                            <tr>
                                <td class="fw-medium">{{ $roomType->name }}</td>
                                <td>
                                    <span class="badge light badge-{{ $roomType->is_active ? 'success' : 'secondary' }}">
                                        {{ $roomType->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('room-types.update')
                                            <a href="{{ route('admin.room-types.edit', $roomType) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('room-types.delete')
                                            <form action="{{ route('admin.room-types.destroy', $roomType) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $roomType->name }}?')">
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
