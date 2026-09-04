@extends('layouts.app')

@section('title', 'Routes')
@section('page-title', 'Routes')

@section('content')
    <x-admin.index-page
        title="Routes"
        card-title="All Routes"
        :create-route="route('admin.routes.create')"
        create-label="New Route"
        create-permission="routes.create"
    >
        <table data-datatable data-empty-message="No routes yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Path</th>
                    <th>Steps</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($routes as $route)
                    <tr>
                        <td class="fw-medium">{{ $route->name }}</td>
                        <td>{{ $route->summary() }}</td>
                        <td>{{ $route->steps->count() }}</td>
                        <td><x-admin.status-badge :active="$route->is_active" /></td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.routes.edit', $route)"
                                :delete-route="route('admin.routes.destroy', $route)"
                                edit-permission="routes.update"
                                delete-permission="routes.delete"
                                :delete-confirm="'Delete '.$route->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
