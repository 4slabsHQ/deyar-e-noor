@extends('layouts.app')

@section('title', 'Hajj Registration')
@section('page-title', 'Hajj Registration')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Hajj Registration</h4>
        @can('pilgrims.create')
            <a href="{{ route('admin.pilgrims.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Registration
            </a>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Registrations</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No registrations yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Family Code</th>
                            <th>Full Name</th>
                            <th>Passport</th>
                            <th>Company</th>
                            <th>Package</th>
                            <th>POD</th>
                            <th>Hajj Year</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pilgrims as $pilgrim)
                            <tr>
                                <td class="fw-medium">{{ $pilgrim->family_code }}</td>
                                <td>{{ $pilgrim->full_name }}</td>
                                <td>{{ $pilgrim->passport_no }}</td>
                                <td>{{ $pilgrim->company?->name }}</td>
                                <td>{{ $pilgrim->package?->name }}</td>
                                <td>{{ $pilgrim->podCity?->name }}</td>
                                <td>{{ $pilgrim->hajj_year }}</td>
                                <td>
                                    <div class="d-flex">
                                        @can('pilgrims.view')
                                            <a href="{{ route('admin.pilgrims.show', $pilgrim) }}"
                                               class="btn btn-info shadow btn-xs sharp me-1"
                                               title="View registration">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan
                                        @can('pilgrims.update')
                                            <a href="{{ route('admin.pilgrims.edit', $pilgrim) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('pilgrims.delete')
                                            <form action="{{ route('admin.pilgrims.destroy', $pilgrim) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $pilgrim->full_name }}?')">
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
