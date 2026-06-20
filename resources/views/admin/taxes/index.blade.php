@extends('layouts.app')

@section('title', 'Taxes')
@section('page-title', 'Taxes')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Taxes</h4>
        @can('taxes.create')
            <a href="{{ route('admin.taxes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Tax
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Taxes</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No taxes yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Rate</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($taxes as $tax)
                            <tr>
                                <td class="fw-medium">{{ $tax->name }}</td>
                                <td>{{ $tax->code ?? '—' }}</td>
                                <td>
                                    {{ $tax->rate }}{{ $tax->type === 'percentage' ? '%' : '' }}
                                </td>
                                <td>{{ ucfirst($tax->type) }}</td>
                                <td>
                                    <span class="badge light badge-{{ $tax->is_active ? 'success' : 'secondary' }}">
                                        {{ $tax->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('taxes.update')
                                            <a href="{{ route('admin.taxes.edit', $tax) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('taxes.delete')
                                            <form action="{{ route('admin.taxes.destroy', $tax) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $tax->name }}?')">
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