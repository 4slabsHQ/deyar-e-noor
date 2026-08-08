@extends('layouts.app')

@section('title', 'Airlines')
@section('page-title', 'Airlines')

@section('content')
    <x-admin.index-page
        title="Airlines"
        card-title="All Airlines"
        :create-route="route('admin.airlines.create')"
        create-label="New Airline"
        create-permission="airlines.create"
    >
        <table data-datatable data-empty-message="No airlines yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>IATA</th>
                    <th>ICAO</th>
                    <th>Country</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($airlines as $airline)
                    <tr>
                        <td>
                            @if ($airline->logo)
                                <img src="{{ Storage::url($airline->logo) }}" alt="{{ $airline->name }}" style="height:30px;">
                            @else
                                —
                            @endif
                        </td>
                        <td class="fw-medium">{{ $airline->name }}</td>
                        <td>{{ $airline->code }}</td>
                        <td>{{ $airline->iata_code ?? '—' }}</td>
                        <td>{{ $airline->icao_code ?? '—' }}</td>
                        <td>{{ $airline->country->name ?? '—' }}</td>
                        <td>
                            <x-admin.status-badge :active="$airline->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.airlines.edit', $airline)"
                                :delete-route="route('admin.airlines.destroy', $airline)"
                                edit-permission="airlines.update"
                                delete-permission="airlines.delete"
                                :delete-confirm="'Delete '.$airline->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
