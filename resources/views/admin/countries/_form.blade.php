@php $country = $country ?? null; @endphp

<x-admin.form-grid>
    <x-admin.form-field label="Name" for="name" class="col-lg-6 col-md-6" :required="true">
        <input type="text" name="name" id="name" value="{{ old('name', $country->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="ISO2 Code" for="iso2" class="col-lg-3 col-md-3" :required="true" hint="2-letter code, e.g. PK">
        <input type="text" name="iso2" id="iso2" maxlength="2" value="{{ old('iso2', $country->iso2 ?? '') }}"
               class="form-control text-uppercase @error('iso2') is-invalid @enderror" required>
        @error('iso2') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="ISO3 Code" for="iso3" class="col-lg-3 col-md-3">
        <input type="text" name="iso3" id="iso3" maxlength="3" value="{{ old('iso3', $country->iso3 ?? '') }}"
               class="form-control text-uppercase @error('iso3') is-invalid @enderror">
        @error('iso3') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Phone Code" for="phone_code" class="col-lg-4 col-md-4">
        <input type="text" name="phone_code" id="phone_code" value="{{ old('phone_code', $country->phone_code ?? '') }}"
               class="form-control @error('phone_code') is-invalid @enderror" placeholder="+92">
        @error('phone_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Flag (emoji)" for="flag" class="col-lg-4 col-md-4">
        <input type="text" name="flag" id="flag" maxlength="10" value="{{ old('flag', $country->flag ?? '') }}"
               class="form-control @error('flag') is-invalid @enderror" placeholder="🇵🇰">
        @error('flag') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Status" class="col-lg-4 col-md-4">
        <x-admin.form-switch :checked="$country->is_active ?? true" inline />
    </x-admin.form-field>
</x-admin.form-grid>
