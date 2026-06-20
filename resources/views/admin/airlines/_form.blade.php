@php $airline = $airline ?? null; @endphp

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Name <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="name" value="{{ old('name', $airline->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Code <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="code" value="{{ old('code', $airline->code ?? '') }}"
               class="form-control text-uppercase @error('code') is-invalid @enderror" required>
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">IATA Code</label>
    <div class="col-lg-8">
        <input type="text" name="iata_code" maxlength="10" value="{{ old('iata_code', $airline->iata_code ?? '') }}"
               class="form-control text-uppercase @error('iata_code') is-invalid @enderror" placeholder="PK">
        @error('iata_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">ICAO Code</label>
    <div class="col-lg-8">
        <input type="text" name="icao_code" maxlength="10" value="{{ old('icao_code', $airline->icao_code ?? '') }}"
               class="form-control text-uppercase @error('icao_code') is-invalid @enderror" placeholder="PIA">
        @error('icao_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Country</label>
    <div class="col-lg-8">
        <select name="country_id" class="form-control @error('country_id') is-invalid @enderror">
            <option value="">-- Select Country --</option>
            @foreach ($countries as $country)
                <option value="{{ $country->id }}"
                    {{ (string) old('country_id', $airline->country_id ?? '') === (string) $country->id ? 'selected' : '' }}>
                    {{ $country->name }}
                </option>
            @endforeach
        </select>
        @error('country_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Logo</label>
    <div class="col-lg-8">
        @if (!empty($airline?->logo))
            <div class="mb-2">
                <img src="{{ Storage::url($airline->logo) }}" alt="{{ $airline->name }}" style="height:50px;">
            </div>
        @endif
        <input type="file" name="logo" accept="image/*"
               class="form-control @error('logo') is-invalid @enderror">
        @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">PNG/JPG, max 2MB. Leave empty to keep current logo.</small>
    </div>
</div>

<div class="mb-3 row">
    <div class="col-lg-3"></div>
    <div class="col-lg-8">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                   {{ old('is_active', $airline->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>