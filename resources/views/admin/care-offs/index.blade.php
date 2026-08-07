@extends('layouts.app')

@section('title', 'Care Offs')
@section('page-title', 'Care Offs')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Care Offs</h4>
        @can('care-offs.create')
            <a href="{{ route('admin.care-offs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Care Off
            </a>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Care Offs</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No care offs yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($careOffs as $careOff)
                            <tr>
                                <td class="fw-medium">{{ $careOff->name }}</td>
                                <td>
                                    <span class="badge light badge-{{ $careOff->is_active ? 'success' : 'secondary' }}">
                                        {{ $careOff->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('care-offs.update')
                                            <a href="{{ route('admin.care-offs.edit', $careOff) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('care-offs.delete')
                                            <form action="{{ route('admin.care-offs.destroy', $careOff) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $careOff->name }}?')">
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
