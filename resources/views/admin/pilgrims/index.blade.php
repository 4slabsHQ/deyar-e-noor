@extends('layouts.app')

@section('title', 'Hajj Registration')
@section('page-title', 'Hajj Registration')

@section('content')
    <x-admin.index-page
        title="Hajj Registration"
        card-title="All Registrations"
        :create-route="route('admin.pilgrims.create')"
        create-label="New Registration"
        create-permission="pilgrims.create"
    >
        <table data-datatable data-empty-message="No registrations yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Family Code</th>
                    <th>Full Name</th>
                    <th>Passport</th>
                    <th>Company</th>
                    <th>Package</th>
                    <th>POD</th>
                    <th>Hajj Year</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pilgrims as $pilgrim)
                    <tr>
                        <td class="fw-medium">{{ $pilgrim->family_code }}</td>
                        <td>{{ $pilgrim->full_name }}</td>
                        <td>{{ $pilgrim->passport_no }}</td>
                        <td>{{ $pilgrim->company?->name }}</td>
                        <td>{{ $pilgrim->package?->name }}</td>
                        <td>{{ $pilgrim->podCity?->name }}</td>
                        <td>{{ $pilgrim->hajj_year }}</td>
                        <td>
                            <x-admin.table-actions
                                :view-route="route('admin.pilgrims.show', $pilgrim)"
                                :edit-route="route('admin.pilgrims.edit', $pilgrim)"
                                :delete-route="route('admin.pilgrims.destroy', $pilgrim)"
                                view-title="View registration"
                                view-permission="pilgrims.view"
                                edit-permission="pilgrims.update"
                                delete-permission="pilgrims.delete"
                                :delete-confirm="'Delete '.$pilgrim->full_name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
