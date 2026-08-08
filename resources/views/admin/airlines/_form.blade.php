@php $airline = $airline ?? null; @endphp

<x-admin.form-grid>
    <x-admin.form-field label="Name" for="name" class="col-lg-4 col-md-6" :required="true">
        <input type="text" name="name" id="name" value="{{ old('name', $airline->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Code" for="code" class="col-lg-2 col-md-3" :required="true">
        <input type="text" name="code" id="code" value="{{ old('code', $airline->code ?? '') }}"
               class="form-control text-uppercase @error('code') is-invalid @enderror" required>
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="IATA Code" for="iata_code" class="col-lg-2 col-md-3">
        <input type="text" name="iata_code" id="iata_code" maxlength="10" value="{{ old('iata_code', $airline->iata_code ?? '') }}"
               class="form-control text-uppercase @error('iata_code') is-invalid @enderror" placeholder="PK">
        @error('iata_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="ICAO Code" for="icao_code" class="col-lg-2 col-md-3">
        <input type="text" name="icao_code" id="icao_code" maxlength="10" value="{{ old('icao_code', $airline->icao_code ?? '') }}"
               class="form-control text-uppercase @error('icao_code') is-invalid @enderror" placeholder="PIA">
        @error('icao_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Country" for="country_id" class="col-lg-4 col-md-6">
        <select name="country_id" id="country_id" class="form-control js-searchable-select @error('country_id') is-invalid @enderror"
                data-placeholder="Select country">
            <option value="" disabled @selected(! old('country_id', $airline->country_id ?? ''))>Select</option>
            @foreach ($countries as $country)
                <option value="{{ $country->id }}"
                    @selected((string) old('country_id', $airline->country_id ?? '') === (string) $country->id)>
                    {{ $country->name }}
                </option>
            @endforeach
        </select>
        @error('country_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Status" class="col-lg-2 col-md-3">
        <x-admin.form-switch :checked="$airline->is_active ?? true" inline />
    </x-admin.form-field>

    <x-admin.form-field label="Logo" for="logo" class="col-lg-6 col-md-8">
        <x-admin.image-upload
            name="logo"
            remove-name="remove_logo"
            :existing-url="! empty($airline?->logo) ? Storage::url($airline->logo) : null"
            hint="PNG/JPG, max 2MB"
            upload-label="Upload logo"
            preview-alt="{{ $airline->name ?? 'Airline logo' }}"
        />
    </x-admin.form-field>
</x-admin.form-grid>
