@php
    use App\Enums\FlightType;

    $viaSectionClass = $selectedType === FlightType::Indirect->value ? '' : 'is-hidden';
@endphp

<x-hajj.form-section title="Via" data-via-section :class="$viaSectionClass">
    <div class="row compact g-2">
        <x-hajj.city-airport-select
            :cities="$cities"
            :airports="$airports"
            city-name="via_city_id"
            city-id="via_city_id"
            airport-name="via_airport_id"
            airport-id="via_airport_id"
            :city-value="$flightModel?->via_city_id"
            :airport-value="$flightModel?->via_airport_id"
            :required="false"
            pair="via"
        />

        <div class="col-lg-2 col-md-3 col-6">
            <label class="form-label" for="via_arrival_date">Arrival Date</label>
            <x-admin.date-input
                name="via_arrival_date"
                id="via_arrival_date"
                :value="old('via_arrival_date', $flightModel?->via_arrival_date?->format('Y-m-d'))"
            />
        </div>

        <div class="col-lg-2 col-md-3 col-6">
            <label class="form-label" for="via_arrival_time">Arrival Time</label>
            <input type="time"
                   name="via_arrival_time"
                   id="via_arrival_time"
                   value="{{ old('via_arrival_time', $flightModel ? $formatTime($flightModel->via_arrival_time) : '') }}"
                   class="form-control @error('via_arrival_time') is-invalid @enderror">
            @error('via_arrival_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-lg-3 col-md-4 col-6">
            <label class="form-label" for="via_airline_id">Airline</label>
            <select name="via_airline_id"
                    id="via_airline_id"
                    class="form-control js-searchable-select @error('via_airline_id') is-invalid @enderror"
                    data-placeholder="Select airline">
                <option value="" disabled {{ old('via_airline_id', $flightModel?->via_airline_id) ? '' : 'selected' }}>Select</option>
                @foreach ($airlines as $airline)
                    <option value="{{ $airline['id'] }}" @selected((int) old('via_airline_id', $flightModel?->via_airline_id) === $airline['id'])>
                        {{ $airline['name'] }}
                    </option>
                @endforeach
            </select>
            @error('via_airline_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <x-hajj.flight-number-input
            name="via_departure_flight_number"
            label="Departing Flight No"
            airline-select-id="via_airline_id"
            prefix-id="via_departure_flight_prefix"
            input-id="via_departure_flight_number"
            placeholder="e.g. 512"
            :value="old('via_departure_flight_number', $viaFlightPart)"
        />

        <div class="col-lg-2 col-md-3 col-6">
            <label class="form-label" for="via_departure_date">Departure Date</label>
            <x-admin.date-input
                name="via_departure_date"
                id="via_departure_date"
                :value="old('via_departure_date', $flightModel?->via_departure_date?->format('Y-m-d'))"
            />
        </div>

        <div class="col-lg-2 col-md-3 col-6">
            <label class="form-label" for="via_departure_time">Departure Time</label>
            <input type="time"
                   name="via_departure_time"
                   id="via_departure_time"
                   value="{{ old('via_departure_time', $flightModel ? $formatTime($flightModel->via_departure_time) : '') }}"
                   class="form-control @error('via_departure_time') is-invalid @enderror">
            @error('via_departure_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-lg-2 col-md-3 col-6">
            <label class="form-label" for="via_total_stay_display">Total Stay</label>
            <input type="text"
                   id="via_total_stay_display"
                   class="form-control calculated-field"
                   data-total-stay-display
                   readonly
                   value="{{ $flightModel?->via_total_stay_label }}"
                   tabindex="-1">
            <span class="form-hint">Calculated from via arrival and departure.</span>
        </div>
    </div>
</x-hajj.form-section>
