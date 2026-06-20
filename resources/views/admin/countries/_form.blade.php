@php $country = $country ?? null; @endphp

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Name <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="name" value="{{ old('name', $country->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">ISO2 Code <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="iso2" maxlength="2" value="{{ old('iso2', $country->iso2 ?? '') }}"
               class="form-control text-uppercase @error('iso2') is-invalid @enderror" required>
        @error('iso2') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">2-letter code, e.g. PK, US</small>
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">ISO3 Code</label>
    <div class="col-lg-8">
        <input type="text" name="iso3" maxlength="3" value="{{ old('iso3', $country->iso3 ?? '') }}"
               class="form-control text-uppercase @error('iso3') is-invalid @enderror">
        @error('iso3') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Phone Code</label>
    <div class="col-lg-8">
        <input type="text" name="phone_code" value="{{ old('phone_code', $country->phone_code ?? '') }}"
               class="form-control @error('phone_code') is-invalid @enderror" placeholder="+92">
        @error('phone_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Flag (emoji)</label>
    <div class="col-lg-8">
        <input type="text" name="flag" maxlength="10" value="{{ old('flag', $country->flag ?? '') }}"
               class="form-control @error('flag') is-invalid @enderror" placeholder="🇵🇰">
        @error('flag') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <div class="col-lg-3"></div>
    <div class="col-lg-8">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                   {{ old('is_active', $country->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>