@extends('layouts.app')

@section('title', 'Flight Assignment')
@section('page-title', 'Flight Assignment')

@section('content')
    <div class="admin-index-page flight-assignment-page"
         id="flight-assignment-page"
         data-workspace-base-url="{{ route('admin.flight-assignments.index') }}">
        <div class="admin-index-header d-flex justify-content-between align-items-center mb-4">
            <h4 class="admin-index-title mb-0">Flight Assignment</h4>
        </div>

        <div class="card admin-index-card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0">Flights</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.flight-assignments.index') }}" class="admin-form mb-3">
                    @if ($selectedFlightId)
                        <input type="hidden" name="flight" value="{{ $selectedFlightId }}">
                    @endif
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="direction" class="admin-form-label">Journey</label>
                            <select name="direction" id="direction" class="form-control">
                                <option value="">All</option>
                                @foreach ($directions as $option)
                                    <option value="{{ $option->value }}" @selected($direction?->value === $option->value)>
                                        {{ $option->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="flight_type" class="admin-form-label">Type</label>
                            <select name="flight_type" id="flight_type" class="form-control">
                                <option value="">All</option>
                                @foreach ($flightTypes as $option)
                                    <option value="{{ $option->value }}" @selected($flightType === $option->value)>
                                        {{ $option->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-auto d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                            @if ($hasFilters)
                                <a href="{{ route('admin.flight-assignments.index', $selectedFlightId ? ['flight' => $selectedFlightId] : []) }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="admin-index-table-wrap flight-assignment-flights-table-wrap">
                    <table data-datatable
                           data-scroll-x="true"
                           data-empty-message="No flights found."
                           class="display flight-assignment-flights-table"
                           style="width:100%">
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
                                <tr @class(['flight-assignment-row', 'is-selected' => $selectedFlightId === $flight->id]) data-flight-id="{{ $flight->id }}">
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
                                    <td data-flight-hujaj-count>{{ number_format($flight->pilgrims_count) }}</td>
                                    <td>{{ $flight->via_total_stay_label ?? '—' }}</td>
                                    <td>
                                        <button type="button"
                                                class="btn btn-primary btn-sm"
                                                data-assign-flight
                                                data-flight-id="{{ $flight->id }}"
                                                data-workspace-url="{{ route('admin.flight-assignments.workspace', $flight) }}">
                                            Assign
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card admin-index-card">
            <div class="card-header">
                <h4 class="card-title mb-0">Assign Hujaj</h4>
            </div>
            <div class="card-body" id="flight-assignment-workspace">
                @if ($workspace)
                    @include('admin.flight-assignments._workspace', $workspace)
                @else
                    <div class="deyar-empty-state py-4" id="flight-assignment-workspace-empty">
                        Select a flight above and click Assign to manage hujaj assignments.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/flight-assignment.js') }}?v=7"></script>
@endpush
