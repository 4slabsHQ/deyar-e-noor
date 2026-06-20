@extends('layouts.app')

@section('title', 'Currencies')
@section('page-title', 'Currencies')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Currencies</h4>
        @can('currencies.create')
            <a href="{{ route('admin.currencies.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Currency
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
            <h4 class="card-title">All Currencies</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No currencies yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Symbol</th>
                            <th>Exchange Rate</th>
                            <th>Default</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($currencies as $currency)
                            <tr>
                                <td class="fw-medium">{{ $currency->name }}</td>
                                <td>{{ $currency->code }}</td>
                                <td>{{ $currency->symbol ?? '—' }}</td>
                                <td>{{ number_format($currency->exchange_rate, 6) }}</td>
                                <td>
                                    @if ($currency->is_default)
                                        <span class="badge light badge-primary">Default</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <span class="badge light badge-{{ $currency->is_active ? 'success' : 'secondary' }}">
                                        {{ $currency->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('currencies.update')
                                            <a href="{{ route('admin.currencies.edit', $currency) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('currencies.delete')
                                            <form action="{{ route('admin.currencies.destroy', $currency) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $currency->name }}?')">
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