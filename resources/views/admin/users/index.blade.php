@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
    <x-admin.index-page
        title="Users"
        card-title="All Users"
        :create-route="route('admin.users.create')"
        create-label="Create User"
        create-permission="users.create"
    >
        <table data-datatable class="display" style="width:100%">
            <thead>
                <tr>
                    <th class="no-sort">Photo</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>
                            <x-user-avatar :user="$user" :size="28" class="users-table-avatar" />
                        </td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->getRoleNames()->first() ?? '—' }}</td>
                        <td>
                            <x-admin.status-badge :active="$user->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.users.edit', $user)"
                                :delete-route="$user->id === auth()->id() ? null : route('admin.users.destroy', $user)"
                                edit-permission="users.update"
                                delete-permission="users.delete"
                                :delete-confirm="'Delete '.$user->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
