@extends('layouts.app')

@section('title', 'Waris Relations')
@section('page-title', 'Waris Relations')

@section('content')
    <x-admin.index-page
        title="Waris Relations"
        card-title="All Waris Relations"
        :create-route="route('admin.waris-relations.create')"
        create-label="New Waris Relation"
        create-permission="waris-relations.create"
    >
        <table data-datatable data-empty-message="No waris relations yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($warisRelations as $warisRelation)
                    <tr>
                        <td class="fw-medium">{{ $warisRelation->name }}</td>
                        <td>
                            <x-admin.status-badge :active="$warisRelation->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.waris-relations.edit', $warisRelation)"
                                :delete-route="route('admin.waris-relations.destroy', $warisRelation)"
                                edit-permission="waris-relations.update"
                                delete-permission="waris-relations.delete"
                                :delete-confirm="'Delete '.$warisRelation->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
