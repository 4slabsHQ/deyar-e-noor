@extends('layouts.app')

@section('title', 'Permissions')
@section('page-title', 'Permissions')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Permissions</h4>
        @can('roles.create')
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Permission
            </a>
        @endcan
    </div>

    {{-- @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif --}}

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Permissions</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No permissions yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Guard</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $permission)
                            <tr>
                                <td>{{ $permission->id }}</td>
                                <td>{{ $permission->name }}</td>
                                <td>{{ $permission->guard_name }}</td>
                                <td>
                                    <div class="d-flex">
                                        @can('roles.update')
                                            <a href="{{ route('admin.permissions.edit', $permission) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('roles.delete')
                                            <form action="{{ route('admin.permissions.destroy', $permission) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $permission->name }}?')">
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