@extends('layouts.app')

@section('title', 'Maktab Categories')
@section('page-title', 'Maktab Categories')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Maktab Categories</h4>
        @can('maktab-categories.create')
            <a href="{{ route('admin.maktab-categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Maktab Category
            </a>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Maktab Categories</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No maktab categories yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Zone</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($maktabCategories as $maktabCategory)
                            <tr>
                                <td class="fw-medium">{{ $maktabCategory->name }}</td>
                                <td>{{ $maktabCategory->zone }}</td>
                                <td>
                                    <span class="badge light badge-{{ $maktabCategory->is_active ? 'success' : 'secondary' }}">
                                        {{ $maktabCategory->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('maktab-categories.update')
                                            <a href="{{ route('admin.maktab-categories.edit', $maktabCategory) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('maktab-categories.delete')
                                            <form action="{{ route('admin.maktab-categories.destroy', $maktabCategory) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $maktabCategory->name }}?')">
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
