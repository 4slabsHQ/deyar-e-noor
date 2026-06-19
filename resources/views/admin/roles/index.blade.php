@extends('layouts.app')

@section('title', 'Roles')
@section('page-title', 'Roles & Permissions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Roles</h4>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">New Role</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
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
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary shadow btn-xs sharp me-1">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                @if ($role->name !== 'Super Admin')
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this role?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection