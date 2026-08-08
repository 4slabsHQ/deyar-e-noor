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
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->getRoleNames()->first() ?? '—' }}</td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.users.edit', $user)"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
