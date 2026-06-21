@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customers')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Customers</h4>
        @can('customers.create')
            <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Customer
            </a>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Customers</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No customers yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Contact</th>
                            <th>Country / City</th>
                            <th>Credit Limit</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td class="fw-medium">{{ $customer->name }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $customer->customer_type)) }}</td>
                                <td>
                                    {{ $customer->phone ?? $customer->email ?? '—' }}
                                </td>
                                <td>{{ $customer->country->name ?? '—' }} / {{ $customer->city->name ?? '—' }}</td>
                                <td>{{ number_format($customer->credit_limit, 2) }}</td>
                                <td>
                                    <span class="badge light badge-{{ $customer->is_active ? 'success' : 'secondary' }}">
                                        {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('customers.update')
                                            <a href="{{ route('admin.customers.edit', $customer) }}"
                                               class="btn btn-primary shadow btn-xs sharp me-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('customers.delete')
                                            <form action="{{ route('admin.customers.destroy', $customer) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $customer->name }}?')">
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