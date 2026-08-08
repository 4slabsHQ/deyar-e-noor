@php $c = $company ?? null; @endphp

<x-admin.form-grid>
    <x-admin.form-field label="Company Name" for="name" class="col-lg-4 col-md-6" :required="true">
        <input type="text" name="name" id="name" value="{{ old('name', $c?->name) }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Company Code" for="code" class="col-lg-2 col-md-3" :required="true" hint="Used in family codes, e.g. DYN-11-A">
        <input type="text" name="code" id="code" value="{{ old('code', $c?->code) }}"
               class="form-control text-uppercase @error('code') is-invalid @enderror" maxlength="20" required placeholder="DYN">
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="ENR Number" for="enr_number" class="col-lg-3 col-md-4">
        <input type="text" name="enr_number" id="enr_number" value="{{ old('enr_number', $c?->enr_number) }}"
               class="form-control @error('enr_number') is-invalid @enderror">
        @error('enr_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Munazzam Code" for="munazzam_code" class="col-lg-3 col-md-4">
        <input type="text" name="munazzam_code" id="munazzam_code" value="{{ old('munazzam_code', $c?->munazzam_code) }}"
               class="form-control @error('munazzam_code') is-invalid @enderror">
        @error('munazzam_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Legal Name" for="legal_name" class="col-lg-6 col-md-6">
        <input type="text" name="legal_name" id="legal_name" value="{{ old('legal_name', $c?->legal_name) }}"
               class="form-control @error('legal_name') is-invalid @enderror">
        @error('legal_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Registration Number" for="registration_number" class="col-lg-6 col-md-6">
        <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number', $c?->registration_number) }}"
               class="form-control @error('registration_number') is-invalid @enderror">
        @error('registration_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Email" for="email" class="col-lg-4 col-md-6">
        <input type="email" name="email" id="email" value="{{ old('email', $c?->email) }}"
               class="form-control @error('email') is-invalid @enderror">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Phone" for="phone" class="col-lg-4 col-md-6">
        <input type="text" name="phone" id="phone" value="{{ old('phone', $c?->phone) }}"
               class="form-control @error('phone') is-invalid @enderror">
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Status" class="col-lg-4 col-md-6">
        <x-admin.form-switch :checked="$c?->is_active ?? true" inline />
    </x-admin.form-field>

    <x-admin.form-field label="Address" for="address" class="col-12">
        <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $c?->address) }}</textarea>
        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Logo" for="logo" class="col-lg-6 col-md-8">
        <x-admin.image-upload
            name="logo"
            remove-name="remove_logo"
            :existing-url="$c?->logo ? Storage::url($c->logo) : null"
            hint="PNG/JPG, max 2MB"
            upload-label="Upload logo"
            preview-alt="Company logo"
        />
    </x-admin.form-field>
</x-admin.form-grid>
