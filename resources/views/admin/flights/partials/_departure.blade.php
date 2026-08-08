<x-hajj.form-section title="Place of Departure">
    <div class="row compact g-2">
        <x-hajj.city-airport-select
            :cities="$cities"
            :airports="$airports"
            city-name="departure_city_id"
            city-id="departure_city_id"
            airport-name="departure_airport_id"
            airport-id="departure_airport_id"
            :city-value="$flightModel?->departure_city_id"
            :airport-value="$flightModel?->departure_airport_id"
            pair="departure"
        />

        <div class="col-lg-3 col-md-4 col-6">
            <label class="form-label" for="departure_airline_id">Airline <span class="text-danger">*</span></label>
            <select name="departure_airline_id"
                    id="departure_airline_id"
                    class="form-control js-searchable-select @error('departure_airline_id') is-invalid @enderror"
                    data-placeholder="Select airline"
                    required>
                <option value="" disabled {{ old('departure_airline_id', $flightModel?->departure_airline_id) ? '' : 'selected' }}>Select</option>
                @foreach ($airlines as $airline)
                    <option value="{{ $airline['id'] }}" @selected((int) old('departure_airline_id', $flightModel?->departure_airline_id) === $airline['id'])>
                        {{ $airline['name'] }}
                    </option>
                @endforeach
            </select>
            @error('departure_airline_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <x-hajj.flight-number-input
            name="departure_flight_number"
            airline-select-id="departure_airline_id"
            prefix-id="departure_flight_prefix"
            :value="old('departure_flight_number', $departureFlightPart)"
            :required="true"
        />

        <div class="col-lg-2 col-md-3 col-6">
            <label class="form-label" for="departure_date">Date of Departure <span class="text-danger">*</span></label>
            <input type="date"
                   name="departure_date"
                   id="departure_date"
                   value="{{ old('departure_date', $flightModel?->departure_date?->format('Y-m-d')) }}"
                   class="form-control @error('departure_date') is-invalid @enderror"
                   required>
            @error('departure_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-lg-2 col-md-3 col-6">
            <label class="form-label" for="departure_time">Time of Departure <span class="text-danger">*</span></label>
            <input type="time"
                   name="departure_time"
                   id="departure_time"
                   value="{{ old('departure_time', $flightModel ? $formatTime($flightModel->departure_time) : '') }}"
                   class="form-control @error('departure_time') is-invalid @enderror"
                   required>
            @error('departure_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</x-hajj.form-section>
