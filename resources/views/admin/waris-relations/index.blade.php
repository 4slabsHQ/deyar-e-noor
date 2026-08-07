@extends('layouts.app')

@section('title', 'Waris Relations')
@section('page-title', 'Waris Relations')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Waris Relations</h4>
        @can('waris-relations.create')
            <a href="{{ route('admin.waris-relations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Waris Relation
            </a>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Waris Relations</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No waris relations yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($warisRelations as $warisRelation)
                            <tr>
                                <td class="fw-medium">{{ $warisRelation->name }}</td>
                                <td>
                                    <span class="badge light badge-{{ $warisRelation->is_active ? 'success' : 'secondary' }}">
                                        {{ $warisRelation->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('waris-relations.update')
                                            <a href="{{ route('admin.waris-relations.edit', $warisRelation) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('waris-relations.delete')
                                            <form action="{{ route('admin.waris-relations.destroy', $warisRelation) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $warisRelation->name }}?')">
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
