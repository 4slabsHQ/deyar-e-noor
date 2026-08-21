@extends('layouts.app')

@section('title', 'Form Owners')
@section('page-title', 'Form Owners')

@section('content')
    <x-admin.index-page
        title="Form Owners"
        card-title="All Form Owners"
        :create-route="route('admin.form-owners.create')"
        create-label="New Form Owner"
        create-permission="form-owners.create"
    >
        <table data-datatable data-empty-message="No form owners yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Limit</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($formOwners as $formOwner)
                    <tr>
                        <td class="fw-medium">{{ $formOwner->name }}</td>
                        <td>{{ $formOwner->limit ?? 'Unlimited' }}</td>
                        <td>
                            <x-admin.status-badge :active="$formOwner->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.form-owners.edit', $formOwner)"
                                :delete-route="route('admin.form-owners.destroy', $formOwner)"
                                edit-permission="form-owners.update"
                                delete-permission="form-owners.delete"
                                :delete-confirm="'Delete '.$formOwner->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
