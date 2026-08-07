@php $maktabCategory = $maktabCategory ?? null; @endphp

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Category <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="name" value="{{ old('name', $maktabCategory->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Zone <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="zone" maxlength="50" value="{{ old('zone', $maktabCategory->zone ?? '') }}"
               class="form-control @error('zone') is-invalid @enderror" required>
        @error('zone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <div class="col-lg-3"></div>
    <div class="col-lg-8">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                   {{ old('is_active', $maktabCategory->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>
