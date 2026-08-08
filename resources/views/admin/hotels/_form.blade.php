@php $hotel = $hotel ?? null; @endphp

<x-admin.form-grid>
    <x-admin.form-field label="Name" for="name" class="col-lg-4 col-md-6" :required="true">
        <input type="text" name="name" id="name" value="{{ old('name', $hotel->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Code" for="code" class="col-lg-2 col-md-3">
        <input type="text" name="code" id="code" value="{{ old('code', $hotel->code ?? '') }}"
               class="form-control @error('code') is-invalid @enderror">
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Star Rating" for="star_rating" class="col-lg-3 col-md-4">
        <select name="star_rating" id="star_rating" class="form-control @error('star_rating') is-invalid @enderror">
            <option value="" disabled @selected(! old('star_rating', $hotel->star_rating ?? ''))>Select</option>
            @foreach ([1, 2, 3, 4, 5] as $star)
                <option value="{{ $star }}"
                    @selected((string) old('star_rating', $hotel->star_rating ?? '') === (string) $star)>
                    {{ $star }} Star
                </option>
            @endforeach
        </select>
        @error('star_rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Status" class="col-lg-3 col-md-4">
        <x-admin.form-switch :checked="$hotel->is_active ?? true" inline />
    </x-admin.form-field>

    <div class="col-12" data-country-city>
        <div class="row g-3">
            <x-admin.form-field label="Country" for="country_id" class="col-lg-6 col-md-6">
                <select name="country_id" id="country_id" data-country-select
                        class="form-control js-searchable-select @error('country_id') is-invalid @enderror"
                        data-placeholder="Select country">
                    <option value="" disabled @selected(! old('country_id', $hotel->country_id ?? ''))>Select</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}"
                            @selected((string) old('country_id', $hotel->country_id ?? '') === (string) $country->id)>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
                @error('country_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </x-admin.form-field>

            <x-admin.form-field label="City" for="city_id" class="col-lg-6 col-md-6">
                <select name="city_id" id="city_id" data-city-select
                        class="form-control js-searchable-select @error('city_id') is-invalid @enderror"
                        data-placeholder="Select city">
                    <option value="" disabled @selected(! old('city_id', $hotel->city_id ?? ''))>Select</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}" data-country-id="{{ $city->country_id }}"
                            @selected((string) old('city_id', $hotel->city_id ?? '') === (string) $city->id)>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
                @error('city_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </x-admin.form-field>
        </div>
    </div>

    <x-admin.form-field label="Address" for="address" class="col-12">
        <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $hotel->address ?? '') }}</textarea>
        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Contact Person" for="contact_person" class="col-lg-4 col-md-6">
        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $hotel->contact_person ?? '') }}"
               class="form-control @error('contact_person') is-invalid @enderror">
        @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Phone" for="phone" class="col-lg-4 col-md-6">
        <input type="text" name="phone" id="phone" value="{{ old('phone', $hotel->phone ?? '') }}"
               class="form-control @error('phone') is-invalid @enderror">
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Email" for="email" class="col-lg-4 col-md-6">
        <input type="email" name="email" id="email" value="{{ old('email', $hotel->email ?? '') }}"
               class="form-control @error('email') is-invalid @enderror">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Website" for="website" class="col-lg-6 col-md-6">
        <input type="url" name="website" id="website" value="{{ old('website', $hotel->website ?? '') }}"
               class="form-control @error('website') is-invalid @enderror" placeholder="https://">
        @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Notes" for="notes" class="col-12">
        <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $hotel->notes ?? '') }}</textarea>
        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>
</x-admin.form-grid>
