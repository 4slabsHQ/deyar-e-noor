@extends('layouts.app')

@section('title', 'Suppliers')
@section('page-title', 'Suppliers')

@section('content')
    <x-admin.index-page
        title="Suppliers"
        card-title="All Suppliers"
        :create-route="route('admin.suppliers.create')"
        create-label="New Supplier"
        create-permission="suppliers.create"
    >
        <table data-datatable data-empty-message="No suppliers yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Contact</th>
                    <th>Country / City</th>
                    <th>Portal Access</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($suppliers as $supplier)
                    <tr>
                        <td class="fw-medium">{{ $supplier->name }}</td>
                        <td>{{ $supplier->category->name ?? '—' }}</td>
                        <td>
                            {{ $supplier->contact_person ?? '—' }}<br>
                            <small class="text-muted">{{ $supplier->phone ?? $supplier->email ?? '' }}</small>
                        </td>
                        <td>{{ $supplier->country->name ?? '—' }} / {{ $supplier->city->name ?? '—' }}</td>
                        <td>
                            <span class="badge light badge-{{ $supplier->portal_access ? 'success' : 'secondary' }}">
                                {{ $supplier->portal_access ? 'Enabled' : 'Disabled' }}
                            </span>
                        </td>
                        <td>
                            <x-admin.status-badge :active="$supplier->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.suppliers.edit', $supplier)"
                                :delete-route="route('admin.suppliers.destroy', $supplier)"
                                edit-permission="suppliers.update"
                                delete-permission="suppliers.delete"
                                :delete-confirm="'Delete '.$supplier->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
