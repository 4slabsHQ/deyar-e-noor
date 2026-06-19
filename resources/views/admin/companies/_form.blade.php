@php $c = $company ?? null; @endphp

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Company Name <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="name" value="{{ old('name', $c?->name) }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Legal Name</label>
    <div class="col-lg-8">
        <input type="text" name="legal_name" value="{{ old('legal_name', $c?->legal_name) }}" class="form-control @error('legal_name') is-invalid @enderror">
        @error('legal_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Registration Number</label>
    <div class="col-lg-8">
        <input type="text" name="registration_number" value="{{ old('registration_number', $c?->registration_number) }}" class="form-control @error('registration_number') is-invalid @enderror">
        @error('registration_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Tax Number</label>
    <div class="col-lg-8">
        <input type="text" name="tax_number" value="{{ old('tax_number', $c?->tax_number) }}" class="form-control @error('tax_number') is-invalid @enderror">
        @error('tax_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Email</label>
    <div class="col-lg-8">
        <input type="email" name="email" value="{{ old('email', $c?->email) }}" class="form-control @error('email') is-invalid @enderror">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Phone</label>
    <div class="col-lg-8">
        <input type="text" name="phone" value="{{ old('phone', $c?->phone) }}" class="form-control @error('phone') is-invalid @enderror">
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Website</label>
    <div class="col-lg-8">
        <input type="url" name="website" value="{{ old('website', $c?->website) }}" class="form-control @error('website') is-invalid @enderror" placeholder="https://example.com">
        @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Address</label>
    <div class="col-lg-8">
        <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $c?->address) }}</textarea>
        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">City</label>
    <div class="col-lg-8">
        <input type="text" name="city" value="{{ old('city', $c?->city) }}" class="form-control @error('city') is-invalid @enderror">
        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">State</label>
    <div class="col-lg-8">
        <input type="text" name="state" value="{{ old('state', $c?->state) }}" class="form-control @error('state') is-invalid @enderror">
        @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Country</label>
    <div class="col-lg-8">
        <input type="text" name="country" value="{{ old('country', $c?->country) }}" class="form-control @error('country') is-invalid @enderror">
        @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Postal Code</label>
    <div class="col-lg-8">
        <input type="text" name="postal_code" value="{{ old('postal_code', $c?->postal_code) }}" class="form-control @error('postal_code') is-invalid @enderror">
        @error('postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Currency</label>
    <div class="col-lg-8">
        <input type="text" name="currency" value="{{ old('currency', $c?->currency ?? 'PKR') }}" class="form-control @error('currency') is-invalid @enderror">
        @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Timezone</label>
    <div class="col-lg-8">
        <input type="text" name="timezone" value="{{ old('timezone', $c?->timezone ?? 'Asia/Karachi') }}" class="form-control @error('timezone') is-invalid @enderror">
        @error('timezone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Logo</label>
    <div class="col-lg-8">
        @if ($c?->logo)
            <img src="{{ Storage::url($c->logo) }}" class="rounded border mb-2 d-block" width="90" height="90" style="object-fit:cover;">
        @endif
        <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
        @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Active</label>
    <div class="col-lg-8">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $c?->is_active ?? true) ? 'checked' : '' }}>
        </div>
    </div>
</div>