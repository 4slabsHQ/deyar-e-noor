@extends('layouts.app')

@section('title', 'Taxes')
@section('page-title', 'Taxes')

@section('content')
    <x-admin.index-page
        title="Taxes"
        card-title="All Taxes"
        :create-route="route('admin.taxes.create')"
        create-label="New Tax"
        create-permission="taxes.create"
    >
        <table data-datatable data-empty-message="No taxes yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Rate</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($taxes as $tax)
                    <tr>
                        <td class="fw-medium">{{ $tax->name }}</td>
                        <td>{{ $tax->code ?? '—' }}</td>
                        <td>
                            {{ $tax->rate }}{{ $tax->type === 'percentage' ? '%' : '' }}
                        </td>
                        <td>{{ ucfirst($tax->type) }}</td>
                        <td>
                            <x-admin.status-badge :active="$tax->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.taxes.edit', $tax)"
                                :delete-route="route('admin.taxes.destroy', $tax)"
                                edit-permission="taxes.update"
                                delete-permission="taxes.delete"
                                :delete-confirm="'Delete '.$tax->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
