@extends('layouts.app')

@section('title', 'Care Offs')
@section('page-title', 'Care Offs')

@section('content')
    <x-admin.index-page
        title="Care Offs"
        card-title="All Care Offs"
        :create-route="route('admin.care-offs.create')"
        create-label="New Care Off"
        create-permission="care-offs.create"
    >
        <table data-datatable data-empty-message="No care offs yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($careOffs as $careOff)
                    <tr>
                        <td class="fw-medium">{{ $careOff->name }}</td>
                        <td>
                            <x-admin.status-badge :active="$careOff->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.care-offs.edit', $careOff)"
                                :delete-route="route('admin.care-offs.destroy', $careOff)"
                                edit-permission="care-offs.update"
                                delete-permission="care-offs.delete"
                                :delete-confirm="'Delete '.$careOff->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
