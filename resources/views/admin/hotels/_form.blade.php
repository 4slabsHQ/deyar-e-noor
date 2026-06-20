@php $hotel = $hotel ?? null; @endphp

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Name <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="name" value="{{ old('name', $hotel->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Code</label>
    <div class="col-lg-8">
        <input type="text" name="code" value="{{ old('code', $hotel->code ?? '') }}"
               class="form-control @error('code') is-invalid @enderror">
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Star Rating</label>
    <div class="col-lg-8">
        <select name="star_rating" class="form-control @error('star_rating') is-invalid @enderror">
            <option value="">-- Select Rating --</option>
            @foreach ([1, 2, 3, 4, 5] as $star)
                <option value="{{ $star }}"
                    {{ (string) old('star_rating', $hotel->star_rating ?? '') === (string) $star ? 'selected' : '' }}>
                    {{ $star }} Star
                </option>
            @endforeach
        </select>
        @error('star_rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Country</label>
    <div class="col-lg-8">
        <select name="country_id" id="country_id" class="form-control @error('country_id') is-invalid @enderror">
            <option value="">-- Select Country --</option>
            @foreach ($countries as $country)
                <option value="{{ $country->id }}"
                    {{ (string) old('country_id', $hotel->country_id ?? '') === (string) $country->id ? 'selected' : '' }}>
                    {{ $country->name }}
                </option>
            @endforeach
        </select>
        @error('country_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">City</label>
    <div class="col-lg-8">
        <select name="city_id" id="city_id" class="form-control @error('city_id') is-invalid @enderror">
            <option value="">-- Select City --</option>
            @foreach ($cities as $city)
                <option value="{{ $city->id }}" data-country-id="{{ $city->country_id }}"
                    {{ (string) old('city_id', $hotel->city_id ?? '') === (string) $city->id ? 'selected' : '' }}>
                    {{ $city->name }}
                </option>
            @endforeach
        </select>
        @error('city_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Address</label>
    <div class="col-lg-8">
        <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $hotel->address ?? '') }}</textarea>
        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Contact Person</label>
    <div class="col-lg-8">
        <input type="text" name="contact_person" value="{{ old('contact_person', $hotel->contact_person ?? '') }}"
               class="form-control @error('contact_person') is-invalid @enderror">
        @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Phone</label>
    <div class="col-lg-8">
        <input type="text" name="phone" value="{{ old('phone', $hotel->phone ?? '') }}"
               class="form-control @error('phone') is-invalid @enderror">
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Email</label>
    <div class="col-lg-8">
        <input type="email" name="email" value="{{ old('email', $hotel->email ?? '') }}"
               class="form-control @error('email') is-invalid @enderror">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Website</label>
    <div class="col-lg-8">
        <input type="url" name="website" value="{{ old('website', $hotel->website ?? '') }}"
               class="form-control @error('website') is-invalid @enderror" placeholder="https://">
        @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Notes</label>
    <div class="col-lg-8">
        <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $hotel->notes ?? '') }}</textarea>
        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <div class="col-lg-3"></div>
    <div class="col-lg-8">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                   {{ old('is_active', $hotel->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const countrySelect = document.getElementById('country_id');
        const citySelect = document.getElementById('city_id');
        const cityOptions = Array.from(citySelect.options);

        function filterCities() {
            const countryId = countrySelect.value;
            const currentCity = citySelect.value;

            citySelect.innerHTML = '';
            citySelect.appendChild(cityOptions[0].cloneNode(true)); // "-- Select City --"

            cityOptions.forEach(function (opt) {
                if (!opt.value) return;
                if (!countryId || opt.dataset.countryId === countryId) {
                    const clone = opt.cloneNode(true);
                    if (clone.value === currentCity) clone.selected = true;
                    citySelect.appendChild(clone);
                }
            });
        }

        countrySelect.addEventListener('change', filterCities);
        filterCities(); // run once on load to respect old()/edit values
    })();
</script>
@endpush