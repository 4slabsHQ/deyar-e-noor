@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customers')

@section('content')
    <x-admin.index-page
        title="Customers"
        card-title="All Customers"
        :create-route="route('admin.customers.create')"
        create-label="New Customer"
        create-permission="customers.create"
    >
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
                            <x-admin.status-badge :active="$customer->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.customers.edit', $customer)"
                                :delete-route="route('admin.customers.destroy', $customer)"
                                edit-permission="customers.update"
                                delete-permission="customers.delete"
                                :delete-confirm="'Delete '.$customer->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
