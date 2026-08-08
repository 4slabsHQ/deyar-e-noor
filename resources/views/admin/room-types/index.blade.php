@extends('layouts.app')

@section('title', 'Room Types')
@section('page-title', 'Room Types')

@section('content')
    <x-admin.index-page
        title="Room Types"
        card-title="All Room Types"
        :create-route="route('admin.room-types.create')"
        create-label="New Room Type"
        create-permission="room-types.create"
    >
        <table data-datatable data-empty-message="No room types yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roomTypes as $roomType)
                    <tr>
                        <td class="fw-medium">{{ $roomType->name }}</td>
                        <td>
                            <x-admin.status-badge :active="$roomType->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.room-types.edit', $roomType)"
                                :delete-route="route('admin.room-types.destroy', $roomType)"
                                edit-permission="room-types.update"
                                delete-permission="room-types.delete"
                                :delete-confirm="'Delete '.$roomType->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
