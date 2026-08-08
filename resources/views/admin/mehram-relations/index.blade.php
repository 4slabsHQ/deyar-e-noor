@extends('layouts.app')

@section('title', 'Mehram Relations')
@section('page-title', 'Mehram Relations')

@section('content')
    <x-admin.index-page
        title="Mehram Relations"
        card-title="All Mehram Relations"
        :create-route="route('admin.mehram-relations.create')"
        create-label="New Mehram Relation"
        create-permission="mehram-relations.create"
    >
        <table data-datatable data-empty-message="No mehram relations yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mehramRelations as $mehramRelation)
                    <tr>
                        <td class="fw-medium">{{ $mehramRelation->name }}</td>
                        <td>
                            <x-admin.status-badge :active="$mehramRelation->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.mehram-relations.edit', $mehramRelation)"
                                :delete-route="route('admin.mehram-relations.destroy', $mehramRelation)"
                                edit-permission="mehram-relations.update"
                                delete-permission="mehram-relations.delete"
                                :delete-confirm="'Delete '.$mehramRelation->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
