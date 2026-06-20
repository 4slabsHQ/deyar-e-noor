@php $currency = $currency ?? null; @endphp

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Name <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="name" value="{{ old('name', $currency->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Code <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="code" maxlength="3" value="{{ old('code', $currency->code ?? '') }}"
               class="form-control text-uppercase @error('code') is-invalid @enderror" required>
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">3-letter ISO code, e.g. USD, PKR</small>
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Symbol</label>
    <div class="col-lg-8">
        <input type="text" name="symbol" maxlength="10" value="{{ old('symbol', $currency->symbol ?? '') }}"
               class="form-control @error('symbol') is-invalid @enderror" placeholder="$">
        @error('symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Exchange Rate <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="number" step="0.000001" min="0" name="exchange_rate"
               value="{{ old('exchange_rate', $currency->exchange_rate ?? 1) }}"
               class="form-control @error('exchange_rate') is-invalid @enderror" required>
        @error('exchange_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <div class="col-lg-3"></div>
    <div class="col-lg-8">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_default" value="1" id="is_default"
                   {{ old('is_default', $currency->is_default ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_default">Set as Default</label>
        </div>
    </div>
</div>

<div class="mb-3 row">
    <div class="col-lg-3"></div>
    <div class="col-lg-8">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                   {{ old('is_active', $currency->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>