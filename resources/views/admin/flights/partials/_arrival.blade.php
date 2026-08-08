<x-hajj.form-section title="Place of Arrival">
    <div class="row compact g-2">
        <x-hajj.city-airport-select
            :cities="$cities"
            :airports="$airports"
            city-name="arrival_city_id"
            city-id="arrival_city_id"
            airport-name="arrival_airport_id"
            airport-id="arrival_airport_id"
            :city-value="$flightModel?->arrival_city_id"
            :airport-value="$flightModel?->arrival_airport_id"
            pair="arrival"
        />

        <div class="col-lg-2 col-md-3 col-6">
            <label class="form-label" for="arrival_date">Date of Arrival <span class="text-danger">*</span></label>
            <input type="date"
                   name="arrival_date"
                   id="arrival_date"
                   value="{{ old('arrival_date', $flightModel?->arrival_date?->format('Y-m-d')) }}"
                   class="form-control @error('arrival_date') is-invalid @enderror"
                   required>
            @error('arrival_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-lg-2 col-md-3 col-6">
            <label class="form-label" for="arrival_time">Time of Arrival <span class="text-danger">*</span></label>
            <input type="time"
                   name="arrival_time"
                   id="arrival_time"
                   value="{{ old('arrival_time', $flightModel ? $formatTime($flightModel->arrival_time) : '') }}"
                   class="form-control @error('arrival_time') is-invalid @enderror"
                   required>
            @error('arrival_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</x-hajj.form-section>
