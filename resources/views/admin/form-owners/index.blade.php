@extends('layouts.app')

@section('title', 'Form Owners')
@section('page-title', 'Form Owners')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Form Owners</h4>
        @can('form-owners.create')
            <a href="{{ route('admin.form-owners.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Form Owner
            </a>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Form Owners</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No form owners yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($formOwners as $formOwner)
                            <tr>
                                <td class="fw-medium">{{ $formOwner->name }}</td>
                                <td>
                                    <span class="badge light badge-{{ $formOwner->is_active ? 'success' : 'secondary' }}">
                                        {{ $formOwner->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('form-owners.update')
                                            <a href="{{ route('admin.form-owners.edit', $formOwner) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('form-owners.delete')
                                            <form action="{{ route('admin.form-owners.destroy', $formOwner) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $formOwner->name }}?')">
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
