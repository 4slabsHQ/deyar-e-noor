@php
    use App\Enums\FlightType;

    $flight = $flight ?? null;
    $isEdit = $flight !== null;

    if ($isEdit) {
        $flight->loadMissing(['departureAirline', 'viaAirline']);
    }

    $flightModel = $flight;
    $selectedType = old('flight_type', $flightModel?->flight_type?->value ?? FlightType::Direct->value);

    $departureFlightPart = old('departure_flight_number');
    if ($departureFlightPart === null && $isEdit && $flight->relationLoaded('departureAirline') && $flight->departureAirline) {
        $departureFlightPart = app(\App\Services\FlightService::class)
            ->flightNumberPart($flight->departureAirline, $flight->departure_flight_no);
    }

    $viaFlightPart = old('via_departure_flight_number');
    if ($viaFlightPart === null && $isEdit && $flight->relationLoaded('viaAirline') && $flight->viaAirline && $flight->via_departure_flight_no) {
        $viaFlightPart = app(\App\Services\FlightService::class)
            ->flightNumberPart($flight->viaAirline, $flight->via_departure_flight_no);
    }

    $formatTime = static function (?string $time): string {
        if ($time === null || $time === '') {
            return '';
        }

        return substr($time, 0, 5);
    };

    $airportsByCity = $airports->groupBy('city_id')->map(
        fn ($group) => $group->map(fn ($airport) => ['id' => $airport->id, 'name' => $airport->name])->values()
    );
@endphp

@push('styles')
    <link href="{{ asset('css/pilgrim-form.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/flight-form.js') }}"></script>
@endpush

<div class="hajj-form"
     data-flight-form
     data-indirect-value="{{ FlightType::Indirect->value }}"
     data-airlines='@json($airlines->values())'
     data-airports-by-city='@json($airportsByCity)'>

    @include('admin.flights.partials._flight-type')
    @include('admin.flights.partials._departure')
    @include('admin.flights.partials._via')
    @include('admin.flights.partials._arrival')
</div>
