@extends('layouts.app')

@section('title', 'Roles')
@section('page-title', 'Roles & Permissions')

@section('content')
    <x-admin.index-page
        title="Roles"
        :create-route="route('admin.roles.create')"
        create-label="New Role"
    >
        <table data-datatable data-empty-message="No roles yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Permissions</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->permissions->count() }} permissions</td>
                        <td>
                            <div class="d-flex">
                                <x-admin.table-actions
                                    :edit-route="route('admin.roles.edit', $role)"
                                />
                                @if ($role->name !== 'Super Admin')
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Delete this role?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger shadow btn-xs sharp" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
