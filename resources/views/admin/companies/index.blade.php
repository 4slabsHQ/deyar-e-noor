@extends('layouts.app')

@section('title', 'Companies')

@section('page-title', 'Companies')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Companies</h4>
        @if (!\App\Models\Company::exists())
            <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Company
            </a>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Companies</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No companies yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($company->logo)
                                            <img src="{{ Storage::url($company->logo) }}"
                                                 class="rounded me-2" width="32" height="32"
                                                 style="object-fit: cover;">
                                        @endif
                                        <div>
                                            <span class="fw-medium">{{ $company->name }}</span>
                                            @if ($company->legal_name)
                                                <br><small class="text-muted">{{ $company->legal_name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $company->email ?? '—' }}</td>
                                <td>{{ $company->country ?? '—' }}</td>
                                <td>
                                    <span class="badge light badge-{{ $company->is_active ? 'success' : 'secondary' }}">
                                        {{ $company->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('admin.companies.edit', $company) }}"
                                           class="btn btn-primary shadow btn-xs sharp me-1">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form action="{{ route('admin.companies.destroy', $company) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete {{ $company->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger shadow btn-xs sharp">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
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