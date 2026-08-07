@extends('layouts.app')

@section('title', 'Mehram Relations')
@section('page-title', 'Mehram Relations')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Mehram Relations</h4>
        @can('mehram-relations.create')
            <a href="{{ route('admin.mehram-relations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Mehram Relation
            </a>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Mehram Relations</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No mehram relations yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mehramRelations as $mehramRelation)
                            <tr>
                                <td class="fw-medium">{{ $mehramRelation->name }}</td>
                                <td>
                                    <span class="badge light badge-{{ $mehramRelation->is_active ? 'success' : 'secondary' }}">
                                        {{ $mehramRelation->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('mehram-relations.update')
                                            <a href="{{ route('admin.mehram-relations.edit', $mehramRelation) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('mehram-relations.delete')
                                            <form action="{{ route('admin.mehram-relations.destroy', $mehramRelation) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $mehramRelation->name }}?')">
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
