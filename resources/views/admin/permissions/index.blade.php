@extends('layouts.app')

@section('title', 'Permissions')
@section('page-title', 'Permissions')

@section('content')
    <x-admin.index-page
        title="Permissions"
        card-title="All Permissions"
        :create-route="route('admin.permissions.create')"
        create-label="New Permission"
        create-permission="roles.create"
    >
        <table data-datatable data-empty-message="No permissions yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Guard</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($permissions as $permission)
                    <tr>
                        <td>{{ $permission->id }}</td>
                        <td>{{ $permission->name }}</td>
                        <td>{{ $permission->guard_name }}</td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.permissions.edit', $permission)"
                                :delete-route="route('admin.permissions.destroy', $permission)"
                                edit-permission="roles.update"
                                delete-permission="roles.delete"
                                :delete-confirm="'Delete '.$permission->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
