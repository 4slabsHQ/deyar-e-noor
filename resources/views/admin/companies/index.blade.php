@extends('layouts.app')

@section('title', 'Companies')

{{-- If your layouts.app has @stack('styles') in <head>, this will load there.
     Otherwise, just leave it here — it'll still work, only rendered inline. --}}
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Companies</h1>
        <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
            + New Company
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Companies</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="companies-table" class="table table-hover display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($company->logo)
                                            <img src="{{ Storage::url($company->logo) }}"
                                                 alt="{{ $company->name }}"
                                                 class="rounded" width="32" height="32"
                                                 style="object-fit: cover;">
                                        @endif
                                        <div>
                                            <div class="fw-medium">{{ $company->name }}</div>
                                            @if ($company->legal_name)
                                                <small class="text-muted">{{ $company->legal_name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $company->email ?? '—' }}</td>
                                <td class="text-muted">{{ $company->country ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $company->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                        {{ $company->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.companies.edit', $company) }}"
                                       class="btn btn-primary shadow btn-xs sharp me-1">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>

                                    <form action="{{ route('admin.companies.destroy', $company) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete {{ $company->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger shadow btn-xs sharp">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

{{-- If your layouts.app has @stack('scripts') near the bottom, this will load there.
     Make sure jQuery is loaded BEFORE this (most Bootstrap admin layouts already include it). --}}
@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(function () {
            $('#companies-table').DataTable({
                // turn off DataTables' own ordering on the Actions column
                columnDefs: [
                    { orderable: false, targets: -1 }
                ],
                language: {
                    emptyTable: 'No companies yet. <a href="{{ route('admin.companies.create') }}">Create one</a>'
                }
            });
        });
    </script>
@endpush