@extends('layouts.app')

@section('title', 'Flights')
@section('page-title', 'Flights')

@section('content')
    <x-admin.index-page
        title="Flights"
        card-title="All Flights"
        :create-route="route('admin.flights.create')"
        create-label="New Flight"
        create-permission="flights.create"
    >
        <table data-datatable data-empty-message="No flights yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Journey</th>
                    <th>Type</th>
                    <th>From</th>
                    <th>Via</th>
                    <th>To</th>
                    <th>Departure</th>
                    <th>Flight No</th>
                    <th>Hujaj</th>
                    <th>Total Stay</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($flights as $flight)
                    <tr>
                        <td>{{ $flight->direction->label() }}</td>
                        <td>{{ $flight->flight_type->label() }}</td>
                        <td>{{ $flight->departureCity?->name }} ({{ $flight->departureAirport?->code }})</td>
                        <td>
                            @if ($flight->flight_type->value === 'indirect')
                                {{ $flight->viaCity?->name }}
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $flight->arrivalCity?->name }} ({{ $flight->arrivalAirport?->code }})</td>
                        <td>{{ $flight->departure_date?->format('d M Y') }} {{ substr((string) $flight->departure_time, 0, 5) }}</td>
                        <td class="fw-medium">{{ $flight->departure_flight_no }}</td>
                        <td>{{ number_format($flight->pilgrims_count) }}</td>
                        <td>{{ $flight->via_total_stay_label ?? '—' }}</td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.flights.edit', $flight)"
                                :delete-route="route('admin.flights.destroy', $flight)"
                                edit-permission="flights.update"
                                delete-permission="flights.delete"
                                delete-confirm="Delete this flight?"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
