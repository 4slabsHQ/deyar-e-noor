@php $tax = $tax ?? null; @endphp

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Name <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="name" value="{{ old('name', $tax->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror"
               placeholder="e.g. VAT 5%" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Code</label>
    <div class="col-lg-8">
        <input type="text" name="code" value="{{ old('code', $tax->code ?? '') }}"
               class="form-control @error('code') is-invalid @enderror"
               placeholder="e.g. VAT5">
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Rate <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="number" step="0.0001" min="0" name="rate"
               value="{{ old('rate', $tax->rate ?? '') }}"
               class="form-control @error('rate') is-invalid @enderror" required>
        @error('rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">For percentage type, enter 5 for 5%. For fixed type, enter the flat amount.</small>
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Type <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <select name="type" class="form-control @error('type') is-invalid @enderror" required>
            <option value="percentage" {{ old('type', $tax->type ?? '') === 'percentage' ? 'selected' : '' }}>
                Percentage
            </option>
            <option value="fixed" {{ old('type', $tax->type ?? '') === 'fixed' ? 'selected' : '' }}>
                Fixed
            </option>
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <div class="col-lg-3"></div>
    <div class="col-lg-8">
        {{-- Hidden 0 ensures "is_active" is sent even when checkbox is unchecked --}}
        <input type="hidden" name="is_active" value="0">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                   {{ old('is_active', $tax->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>