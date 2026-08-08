@php $airport = $airport ?? null; @endphp

<x-admin.form-grid>
    <x-admin.form-field label="Name" for="name" class="col-lg-5 col-md-6" :required="true">
        <input type="text" name="name" id="name" value="{{ old('name', $airport->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Code" for="code" class="col-lg-2 col-md-3" :required="true">
        <input type="text" name="code" id="code" maxlength="10" value="{{ old('code', $airport->code ?? '') }}"
               class="form-control text-uppercase @error('code') is-invalid @enderror" required placeholder="LHE">
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="City" for="city_id" class="col-lg-3 col-md-6" :required="true">
        <select name="city_id" id="city_id" class="form-control js-searchable-select @error('city_id') is-invalid @enderror"
                data-placeholder="Select city" required>
            <option value="" disabled @selected(! old('city_id', $airport->city_id ?? ''))>Select</option>
            @foreach ($cities as $city)
                <option value="{{ $city->id }}"
                    @selected((string) old('city_id', $airport->city_id ?? '') === (string) $city->id)>
                    {{ $city->name }}
                </option>
            @endforeach
        </select>
        @error('city_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Status" class="col-lg-2 col-md-3">
        <x-admin.form-switch :checked="$airport->is_active ?? true" inline />
    </x-admin.form-field>
</x-admin.form-grid>
