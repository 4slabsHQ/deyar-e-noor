@php $c = $company ?? null; @endphp

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Company Name <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="name" value="{{ old('name', $c?->name) }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Company Code <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="code" value="{{ old('code', $c?->code) }}" class="form-control text-uppercase @error('code') is-invalid @enderror" maxlength="20" required placeholder="DYN">
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">Used in family codes, e.g. DYN-11-A</small>
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">ENR Number</label>
    <div class="col-lg-8">
        <input type="text" name="enr_number" value="{{ old('enr_number', $c?->enr_number) }}" class="form-control @error('enr_number') is-invalid @enderror">
        @error('enr_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Munazzam Code</label>
    <div class="col-lg-8">
        <input type="text" name="munazzam_code" value="{{ old('munazzam_code', $c?->munazzam_code) }}" class="form-control @error('munazzam_code') is-invalid @enderror">
        @error('munazzam_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
    <label class="col-lg-3 col-form-label">Address</label>
    <div class="col-lg-8">
        <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $c?->address) }}</textarea>
        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
