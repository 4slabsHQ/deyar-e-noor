@extends('layouts.app')

@section('title', 'Suppliers')
@section('page-title', 'Suppliers')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Suppliers</h4>
        @can('suppliers.create')
            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Supplier
            </a>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Suppliers</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No suppliers yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Contact</th>
                            <th>Country / City</th>
                            <th>Portal Access</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr>
                                <td class="fw-medium">{{ $supplier->name }}</td>
                                <td>{{ $supplier->category->name ?? '—' }}</td>
                                <td>
                                    {{ $supplier->contact_person ?? '—' }}<br>
                                    <small class="text-muted">{{ $supplier->phone ?? $supplier->email ?? '' }}</small>
                                </td>
                                <td>{{ $supplier->country->name ?? '—' }} / {{ $supplier->city->name ?? '—' }}</td>
                                <td>
                                    <span class="badge light badge-{{ $supplier->portal_access ? 'success' : 'secondary' }}">
                                        {{ $supplier->portal_access ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge light badge-{{ $supplier->is_active ? 'success' : 'secondary' }}">
                                        {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('suppliers.update')
                                            <a href="{{ route('admin.suppliers.edit', $supplier) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('suppliers.delete')
                                            <form action="{{ route('admin.suppliers.destroy', $supplier) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $supplier->name }}?')">
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