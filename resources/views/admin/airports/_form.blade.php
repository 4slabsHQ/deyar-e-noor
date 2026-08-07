@php $airport = $airport ?? null; @endphp

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Name <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="name" value="{{ old('name', $airport->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Code <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="code" maxlength="10" value="{{ old('code', $airport->code ?? '') }}"
               class="form-control text-uppercase @error('code') is-invalid @enderror" required placeholder="LHE">
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">City <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <select name="city_id" class="form-control @error('city_id') is-invalid @enderror" required>
            <option value="">-- Select City --</option>
            @foreach ($cities as $city)
                <option value="{{ $city->id }}"
                    {{ (string) old('city_id', $airport->city_id ?? '') === (string) $city->id ? 'selected' : '' }}>
                    {{ $city->name }}
                </option>
            @endforeach
        </select>
        @error('city_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <div class="col-lg-3"></div>
    <div class="col-lg-8">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                   {{ old('is_active', $airport->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>
