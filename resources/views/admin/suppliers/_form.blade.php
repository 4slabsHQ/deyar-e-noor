@php $supplier = $supplier ?? null; @endphp

<h5 class="mb-3 text-primary">Basic Information</h5>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Name <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Code</label>
    <div class="col-lg-8">
        <input type="text" name="code" value="{{ old('code', $supplier->code ?? '') }}"
               class="form-control @error('code') is-invalid @enderror">
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Category</label>
    <div class="col-lg-8">
        <select name="supplier_category_id" class="form-control @error('supplier_category_id') is-invalid @enderror">
            <option value="">-- Select Category --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    {{ (string) old('supplier_category_id', $supplier->supplier_category_id ?? '') === (string) $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('supplier_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<hr class="my-4">
<h5 class="mb-3 text-primary">Contact Details</h5>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Contact Person</label>
    <div class="col-lg-8">
        <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person ?? '') }}"
               class="form-control @error('contact_person') is-invalid @enderror">
        @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Email</label>
    <div class="col-lg-8">
        <input type="email" name="email" value="{{ old('email', $supplier->email ?? '') }}"
               class="form-control @error('email') is-invalid @enderror">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Phone</label>
    <div class="col-lg-8">
        <input type="text" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}"
               class="form-control @error('phone') is-invalid @enderror">
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">WhatsApp</label>
    <div class="col-lg-8">
        <input type="text" name="whatsapp" value="{{ old('whatsapp', $supplier->whatsapp ?? '') }}"
               class="form-control @error('whatsapp') is-invalid @enderror">
        @error('whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Address</label>
    <div class="col-lg-8">
        <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $supplier->address ?? '') }}</textarea>
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
                    {{ (string) old('country_id', $supplier->country_id ?? '') === (string) $country->id ? 'selected' : '' }}>
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
                    {{ (string) old('city_id', $supplier->city_id ?? '') === (string) $city->id ? 'selected' : '' }}>
                    {{ $city->name }}
                </option>
            @endforeach
        </select>
        @error('city_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<hr class="my-4">
<h5 class="mb-3 text-primary">Tax & Banking</h5>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Tax Number</label>
    <div class="col-lg-8">
        <input type="text" name="tax_number" value="{{ old('tax_number', $supplier->tax_number ?? '') }}"
               class="form-control @error('tax_number') is-invalid @enderror">
        @error('tax_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Bank Name</label>
    <div class="col-lg-8">
        <input type="text" name="bank_name" value="{{ old('bank_name', $supplier->bank_name ?? '') }}"
               class="form-control @error('bank_name') is-invalid @enderror">
        @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Bank Account</label>
    <div class="col-lg-8">
        <input type="text" name="bank_account" value="{{ old('bank_account', $supplier->bank_account ?? '') }}"
               class="form-control @error('bank_account') is-invalid @enderror">
        @error('bank_account') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Bank IBAN</label>
    <div class="col-lg-8">
        <input type="text" name="bank_iban" value="{{ old('bank_iban', $supplier->bank_iban ?? '') }}"
               class="form-control @error('bank_iban') is-invalid @enderror">
        @error('bank_iban') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<hr class="my-4">
<h5 class="mb-3 text-primary">Supplier Portal Access</h5>
<p class="text-muted small">These fields control whether this supplier can log into the supplier portal once it's built.</p>

<div class="mb-3 row">
    <div class="col-lg-3"></div>
    <div class="col-lg-8">
        <input type="hidden" name="portal_access" value="0">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="portal_access" value="1" id="portal_access"
                   {{ old('portal_access', $supplier->portal_access ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="portal_access">Allow Portal Access</label>
        </div>
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Portal Email</label>
    <div class="col-lg-8">
        <input type="email" name="portal_email" value="{{ old('portal_email', $supplier->portal_email ?? '') }}"
               class="form-control @error('portal_email') is-invalid @enderror">
        @error('portal_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">The email the supplier will use to log in.</small>
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Portal Password</label>
    <div class="col-lg-8">
        <input type="password" name="portal_password"
               class="form-control @error('portal_password') is-invalid @enderror"
               placeholder="{{ $supplier ? 'Leave blank to keep current password' : 'Minimum 8 characters' }}">
        @error('portal_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<hr class="my-4">
<h5 class="mb-3 text-primary">Other</h5>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Notes</label>
    <div class="col-lg-8">
        <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $supplier->notes ?? '') }}</textarea>
        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <div class="col-lg-3"></div>
    <div class="col-lg-8">
        <input type="hidden" name="is_active" value="0">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                   {{ old('is_active', $supplier->is_active ?? true) ? 'checked' : '' }}>
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