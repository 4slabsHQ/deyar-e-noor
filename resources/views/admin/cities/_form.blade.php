@php $city = $city ?? null; @endphp

<x-admin.form-grid>
    <x-admin.form-field label="Name" for="name" class="col-lg-5 col-md-6" :required="true">
        <input type="text" name="name" id="name" value="{{ old('name', $city?->name) }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Country" for="country_id" class="col-lg-5 col-md-6" :required="true">
        <select name="country_id" id="country_id"
                class="form-control js-searchable-select @error('country_id') is-invalid @enderror"
                data-placeholder="Select country" required>
            <option value="" disabled @selected(! old('country_id', $city?->country_id))>Select</option>
            @foreach ($countries as $country)
                <option value="{{ $country->id }}"
                    @selected((string) old('country_id', $city?->country_id) === (string) $country->id)>
                    {{ $country->name }}
                </option>
            @endforeach
        </select>
        @error('country_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Status" class="col-lg-2 col-md-4">
        <x-admin.form-switch :checked="$city?->is_active ?? true" :send-unchecked="true" inline />
    </x-admin.form-field>
</x-admin.form-grid>
