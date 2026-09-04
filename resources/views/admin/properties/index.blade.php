@extends('layouts.app')

@section('title', 'Properties')
@section('page-title', 'Properties')

@section('content')
    <x-admin.index-page
        title="Properties"
        card-title="All Properties"
        :create-route="route('admin.properties.create')"
        create-label="New Property"
        create-permission="properties.create"
    >
        <table data-datatable data-empty-message="No properties yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>City</th>
                    <th>Type</th>
                    <th>Akads</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($properties as $property)
                    <tr>
                        <td class="fw-medium">{{ $property->name }}</td>
                        <td>{{ $property->city->label() }}</td>
                        <td>{{ $property->type->label() }}</td>
                        <td>{{ $property->akads_count }}</td>
                        <td><x-admin.status-badge :active="$property->is_active" /></td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.properties.edit', $property)"
                                :delete-route="route('admin.properties.destroy', $property)"
                                edit-permission="properties.update"
                                delete-permission="properties.delete"
                                :delete-confirm="'Delete '.$property->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
