@extends('layouts.app')

@section('title', 'Cities')
@section('page-title', 'Cities')

@section('content')
    <x-admin.index-page
        title="Cities"
        card-title="All Cities"
        :create-route="route('admin.cities.create')"
        create-label="New City"
        create-permission="cities.create"
    >
        <table data-datatable data-empty-message="No cities yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Country</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cities as $city)
                    <tr>
                        <td class="fw-medium">{{ $city->name }}</td>
                        <td>{{ $city->country->name ?? '—' }}</td>
                        <td>
                            <x-admin.status-badge :active="$city->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.cities.edit', $city)"
                                :delete-route="route('admin.cities.destroy', $city)"
                                edit-permission="cities.update"
                                delete-permission="cities.delete"
                                delete-confirm="Are you sure you want to delete this city?"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
