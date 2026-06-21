@php $customer = $customer ?? null; @endphp

<h5 class="mb-3 text-primary">Basic Information</h5>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Name <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Code</label>
    <div class="col-lg-8">
        <input type="text" name="code" value="{{ old('code', $customer->code ?? '') }}"
               class="form-control @error('code') is-invalid @enderror">
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Customer Type <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <select name="customer_type" class="form-control @error('customer_type') is-invalid @enderror" required>
            @foreach (['individual' => 'Individual', 'corporate' => 'Corporate', 'walk_in' => 'Walk-in'] as $value => $label)
                <option value="{{ $value }}"
                    {{ old('customer_type', $customer->customer_type ?? 'individual') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('customer_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Company Name</label>
    <div class="col-lg-8">
        <input type="text" name="company_name" value="{{ old('company_name', $customer->company_name ?? '') }}"
               class="form-control @error('company_name') is-invalid @enderror">
        @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">Only applicable for corporate customers.</small>
    </div>
</div>

<hr class="my-4">
<h5 class="mb-3 text-primary">Contact Details</h5>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Email</label>
    <div class="col-lg-8">
        <input type="email" name="email" value="{{ old('email', $customer->email ?? '') }}"
               class="form-control @error('email') is-invalid @enderror">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Phone</label>
    <div class="col-lg-8">
        <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
               class="form-control @error('phone') is-invalid @enderror">
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">WhatsApp</label>
    <div class="col-lg-8">
        <input type="text" name="whatsapp" value="{{ old('whatsapp', $customer->whatsapp ?? '') }}"
               class="form-control @error('whatsapp') is-invalid @enderror">
        @error('whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Address</label>
    <div class="col-lg-8">
        <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $customer->address ?? '') }}</textarea>
        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Country</label>
    <div class="col-lg-8">
        <select name="country_id" id="country_id" class="form-control @error('country_id') is-invalid @enderror">
            <option value="">-- Select Country --</option>
            @foreach ($countries as $country)
                <option value="{{ $country->id }}"
                    {{ (string) old('country_id', $customer->country_id ?? '') === (string) $country->id ? 'selected' : '' }}>
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
                    {{ (string) old('city_id', $customer->city_id ?? '') === (string) $city->id ? 'selected' : '' }}>
                    {{ $city->name }}
                </option>
            @endforeach
        </select>
        @error('city_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Nationality</label>
    <div class="col-lg-8">
        <input type="text" name="nationality" value="{{ old('nationality', $customer->nationality ?? '') }}"
               class="form-control @error('nationality') is-invalid @enderror">
        @error('nationality') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<hr class="my-4">
<h5 class="mb-3 text-primary">Identity Details</h5>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Passport Number</label>
    <div class="col-lg-8">
        <input type="text" name="passport_number" value="{{ old('passport_number', $customer->passport_number ?? '') }}"
               class="form-control @error('passport_number') is-invalid @enderror">
        @error('passport_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">CNIC</label>
    <div class="col-lg-8">
        <input type="text" name="cnic" value="{{ old('cnic', $customer->cnic ?? '') }}"
               class="form-control @error('cnic') is-invalid @enderror">
        @error('cnic') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Date of Birth</label>
    <div class="col-lg-8">
        <input type="date" name="dob" value="{{ old('dob', optional($customer?->dob)->format('Y-m-d')) }}"
               class="form-control @error('dob') is-invalid @enderror">
        @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Gender</label>
    <div class="col-lg-8">
        <select name="gender" class="form-control @error('gender') is-invalid @enderror">
            <option value="">-- Select Gender --</option>
            @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                <option value="{{ $value }}"
                    {{ old('gender', $customer->gender ?? '') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<hr class="my-4">
<h5 class="mb-3 text-primary">Tax & Credit</h5>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Tax Number</label>
    <div class="col-lg-8">
        <input type="text" name="tax_number" value="{{ old('tax_number', $customer->tax_number ?? '') }}"
               class="form-control @error('tax_number') is-invalid @enderror">
        @error('tax_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Credit Limit</label>
    <div class="col-lg-8">
        <input type="number" step="0.01" min="0" name="credit_limit"
               value="{{ old('credit_limit', $customer->credit_limit ?? 0) }}"
               class="form-control @error('credit_limit') is-invalid @enderror">
        @error('credit_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Credit Days</label>
    <div class="col-lg-8">
        <input type="number" min="0" name="credit_days"
               value="{{ old('credit_days', $customer->credit_days ?? 0) }}"
               class="form-control @error('credit_days') is-invalid @enderror">
        @error('credit_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<hr class="my-4">
<h5 class="mb-3 text-primary">Other</h5>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Notes</label>
    <div class="col-lg-8">
        <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $customer->notes ?? '') }}</textarea>
        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <div class="col-lg-3"></div>
    <div class="col-lg-8">
        <input type="hidden" name="is_active" value="0">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                   {{ old('is_active', $customer->is_active ?? true) ? 'checked' : '' }}>
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
            citySelect.appendChild(cityOptions[0].cloneNode(true));

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
        filterCities();
    })();
</script>
@endpush