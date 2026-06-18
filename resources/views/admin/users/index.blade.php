@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">All Users</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
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
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary shadow btn-xs sharp">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection