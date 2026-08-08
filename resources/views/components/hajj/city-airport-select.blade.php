@props([
    'cities',
    'airports',
    'cityName',
    'cityId',
    'airportName',
    'airportId',
    'cityValue' => null,
    'airportValue' => null,
    'required' => true,
    'pair' => null,
])

@php
    $pairKey = $pair ?? $cityId;
@endphp

<div {{ $attributes->merge(['class' => 'col-lg-2 col-md-3 col-6']) }}>
    <label class="form-label" for="{{ $cityId }}">
        City @if($required)<span class="text-danger">*</span>@endif
    </label>
    <select name="{{ $cityName }}"
            id="{{ $cityId }}"
            class="form-control js-searchable-select @error($cityName) is-invalid @enderror"
            data-placeholder="Select city"
            data-city-airport-city
            data-city-airport-pair="{{ $pairKey }}"
            @if($required) required @endif>
        <option value="" disabled {{ $cityValue ? '' : 'selected' }}>Select</option>
        @foreach ($cities as $city)
            <option value="{{ $city->id }}" @selected((int) old($cityName, $cityValue) === $city->id)>
                {{ $city->name }}
            </option>
        @endforeach
    </select>
    @error($cityName)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-lg-3 col-md-4 col-6">
    <label class="form-label" for="{{ $airportId }}">
        Airport @if($required)<span class="text-danger">*</span>@endif
    </label>
    <select name="{{ $airportName }}"
            id="{{ $airportId }}"
            class="form-control js-searchable-select @error($airportName) is-invalid @enderror"
            data-placeholder="Select airport"
            data-city-airport-airport
            data-city-airport-pair="{{ $pairKey }}"
            @if($required) required @endif>
        <option value="" disabled {{ $airportValue ? '' : 'selected' }}>Select</option>
        @foreach ($airports as $airport)
            <option value="{{ $airport->id }}"
                    data-city-id="{{ $airport->city_id }}"
                    @selected((int) old($airportName, $airportValue) === $airport->id)>
                {{ $airport->name }}
            </option>
        @endforeach
    </select>
    @error($airportName)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
