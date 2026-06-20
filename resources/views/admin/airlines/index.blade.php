@extends('layouts.app')

@section('title', 'Airlines')
@section('page-title', 'Airlines')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Airlines</h4>
        @can('airlines.create')
            <a href="{{ route('admin.airlines.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Airline
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
            <h4 class="card-title">All Airlines</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No airlines yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>IATA</th>
                            <th>ICAO</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($airlines as $airline)
                            <tr>
                                <td>
                                    @if ($airline->logo)
                                        <img src="{{ Storage::url($airline->logo) }}" alt="{{ $airline->name }}" style="height:30px;">
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="fw-medium">{{ $airline->name }}</td>
                                <td>{{ $airline->code }}</td>
                                <td>{{ $airline->iata_code ?? '—' }}</td>
                                <td>{{ $airline->icao_code ?? '—' }}</td>
                                <td>{{ $airline->country->name ?? '—' }}</td>
                                <td>
                                    <span class="badge light badge-{{ $airline->is_active ? 'success' : 'secondary' }}">
                                        {{ $airline->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('airlines.update')
                                            <a href="{{ route('admin.airlines.edit', $airline) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('airlines.delete')
                                            <form action="{{ route('admin.airlines.destroy', $airline) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $airline->name }}?')">
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