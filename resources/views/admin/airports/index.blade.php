@extends('layouts.app')

@section('title', 'Airports')
@section('page-title', 'Airports')

@section('content')
    <x-admin.index-page
        title="Airports"
        card-title="All Airports"
        :create-route="route('admin.airports.create')"
        create-label="New Airport"
        create-permission="airports.create"
    >
        <table data-datatable data-empty-message="No airports yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>City</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($airports as $airport)
                    <tr>
                        <td class="fw-medium">{{ $airport->name }}</td>
                        <td>{{ $airport->code }}</td>
                        <td>{{ $airport->city->name ?? '—' }}</td>
                        <td>
                            <x-admin.status-badge :active="$airport->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.airports.edit', $airport)"
                                :delete-route="route('admin.airports.destroy', $airport)"
                                edit-permission="airports.update"
                                delete-permission="airports.delete"
                                :delete-confirm="'Delete '.$airport->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
