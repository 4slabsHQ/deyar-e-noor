@extends('layouts.app')

@section('title', 'Countries')
@section('page-title', 'Countries')

@section('content')
    <x-admin.index-page
        title="Countries"
        card-title="All Countries"
        :create-route="route('admin.countries.create')"
        create-label="New Country"
        create-permission="countries.create"
    >
        <table data-datatable data-empty-message="No countries yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Flag</th>
                    <th>Name</th>
                    <th>ISO2</th>
                    <th>ISO3</th>
                    <th>Phone Code</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($countries as $country)
                    <tr>
                        <td>{{ $country->flag ?? '—' }}</td>
                        <td class="fw-medium">{{ $country->name }}</td>
                        <td>{{ $country->iso2 ?? '—' }}</td>
                        <td>{{ $country->iso3 ?? '—' }}</td>
                        <td>{{ $country->phone_code ?? '—' }}</td>
                        <td>
                            <x-admin.status-badge :active="$country->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.countries.edit', $country)"
                                :delete-route="route('admin.countries.destroy', $country)"
                                edit-permission="countries.update"
                                delete-permission="countries.delete"
                                :delete-confirm="'Delete '.$country->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
