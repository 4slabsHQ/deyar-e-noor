@extends('layouts.app')

@section('title', 'Countries')
@section('page-title', 'Countries')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Countries</h4>
        @can('countries.create')
            <a href="{{ route('admin.countries.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Country
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
            <h4 class="card-title">All Countries</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No countries yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Flag</th>
                            <th>Name</th>
                            <th>ISO2</th>
                            <th>ISO3</th>
                            <th>Phone Code</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($countries as $country)
                            <tr>
                                <td>{{ $country->flag ?? '—' }}</td>
                                <td class="fw-medium">{{ $country->name }}</td>
                                <td>{{ $country->iso2 ?? '—' }}</td>
                                <td>{{ $country->iso3 ?? '—' }}</td>
                                <td>{{ $country->phone_code ?? '—' }}</td>
                                <td>
                                    <span class="badge light badge-{{ $country->is_active ? 'success' : 'secondary' }}">
                                        {{ $country->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('countries.update')
                                            <a href="{{ route('admin.countries.edit', $country) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('countries.delete')
                                            <form action="{{ route('admin.countries.destroy', $country) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $country->name }}?')">
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