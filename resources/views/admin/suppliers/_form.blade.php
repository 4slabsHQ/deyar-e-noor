@php $supplier = $supplier ?? null; @endphp

<x-admin.form-section title="Basic Information">
    <x-admin.form-grid>
        <x-admin.form-field label="Name" for="name" class="col-lg-4 col-md-6" :required="true">
            <input type="text" name="name" id="name" value="{{ old('name', $supplier->name ?? '') }}"
                   class="form-control @error('name') is-invalid @enderror" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Code" for="code" class="col-lg-2 col-md-3">
            <input type="text" name="code" id="code" value="{{ old('code', $supplier->code ?? '') }}"
                   class="form-control @error('code') is-invalid @enderror">
            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Category" for="supplier_category_id" class="col-lg-6 col-md-6">
            <select name="supplier_category_id" id="supplier_category_id"
                    class="form-control js-searchable-select @error('supplier_category_id') is-invalid @enderror"
                    data-placeholder="Select category">
                <option value="" disabled @selected(! old('supplier_category_id', $supplier->supplier_category_id ?? ''))>Select</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                        @selected((string) old('supplier_category_id', $supplier->supplier_category_id ?? '') === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('supplier_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>
    </x-admin.form-grid>
</x-admin.form-section>

<x-admin.form-section title="Contact Details">
    <x-admin.form-grid>
        <x-admin.form-field label="Contact Person" for="contact_person" class="col-lg-4 col-md-6">
            <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $supplier->contact_person ?? '') }}"
                   class="form-control @error('contact_person') is-invalid @enderror">
            @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Email" for="email" class="col-lg-4 col-md-6">
            <input type="email" name="email" id="email" value="{{ old('email', $supplier->email ?? '') }}"
                   class="form-control @error('email') is-invalid @enderror">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Phone" for="phone" class="col-lg-4 col-md-6">
            <input type="text" name="phone" id="phone" value="{{ old('phone', $supplier->phone ?? '') }}"
                   class="form-control @error('phone') is-invalid @enderror">
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="WhatsApp" for="whatsapp" class="col-lg-4 col-md-6">
            <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $supplier->whatsapp ?? '') }}"
                   class="form-control @error('whatsapp') is-invalid @enderror">
            @error('whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <div class="col-12" data-country-city>
            <div class="row g-3">
                <x-admin.form-field label="Country" for="country_id" class="col-lg-6 col-md-6">
                    <select name="country_id" id="country_id" data-country-select
                            class="form-control js-searchable-select @error('country_id') is-invalid @enderror"
                            data-placeholder="Select country">
                        <option value="" disabled @selected(! old('country_id', $supplier->country_id ?? ''))>Select</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}"
                                @selected((string) old('country_id', $supplier->country_id ?? '') === (string) $country->id)>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('country_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </x-admin.form-field>

                <x-admin.form-field label="City" for="city_id" class="col-lg-6 col-md-6">
                    <select name="city_id" id="city_id" data-city-select
                            class="form-control js-searchable-select @error('city_id') is-invalid @enderror"
                            data-placeholder="Select city">
                        <option value="" disabled @selected(! old('city_id', $supplier->city_id ?? ''))>Select</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" data-country-id="{{ $city->country_id }}"
                                @selected((string) old('city_id', $supplier->city_id ?? '') === (string) $city->id)>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </x-admin.form-field>
            </div>
        </div>

        <x-admin.form-field label="Address" for="address" class="col-12">
            <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $supplier->address ?? '') }}</textarea>
            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>
    </x-admin.form-grid>
</x-admin.form-section>

<x-admin.form-section title="Tax & Banking">
    <x-admin.form-grid>
        <x-admin.form-field label="Tax Number" for="tax_number" class="col-lg-3 col-md-6">
            <input type="text" name="tax_number" id="tax_number" value="{{ old('tax_number', $supplier->tax_number ?? '') }}"
                   class="form-control @error('tax_number') is-invalid @enderror">
            @error('tax_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Bank Name" for="bank_name" class="col-lg-3 col-md-6">
            <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $supplier->bank_name ?? '') }}"
                   class="form-control @error('bank_name') is-invalid @enderror">
            @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Bank Account" for="bank_account" class="col-lg-3 col-md-6">
            <input type="text" name="bank_account" id="bank_account" value="{{ old('bank_account', $supplier->bank_account ?? '') }}"
                   class="form-control @error('bank_account') is-invalid @enderror">
            @error('bank_account') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Bank IBAN" for="bank_iban" class="col-lg-3 col-md-6">
            <input type="text" name="bank_iban" id="bank_iban" value="{{ old('bank_iban', $supplier->bank_iban ?? '') }}"
                   class="form-control @error('bank_iban') is-invalid @enderror">
            @error('bank_iban') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>
    </x-admin.form-grid>
</x-admin.form-section>

<x-admin.form-section title="Supplier Portal Access">
    <p class="form-hint mb-3">These fields control whether this supplier can log into the supplier portal once it's built.</p>

    <x-admin.form-grid>
        <x-admin.form-field label="Portal Access" class="col-lg-4 col-md-6">
            <x-admin.form-switch name="portal_access" label="Allow Portal Access" :checked="$supplier->portal_access ?? false" :send-unchecked="true" inline />
        </x-admin.form-field>

        <x-admin.form-field label="Portal Email" for="portal_email" class="col-lg-4 col-md-6" hint="Login email for the supplier portal.">
            <input type="email" name="portal_email" id="portal_email" value="{{ old('portal_email', $supplier->portal_email ?? '') }}"
                   class="form-control @error('portal_email') is-invalid @enderror">
            @error('portal_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Portal Password" for="portal_password" class="col-lg-4 col-md-6">
            <input type="password" name="portal_password" id="portal_password"
                   class="form-control @error('portal_password') is-invalid @enderror"
                   placeholder="{{ $supplier ? 'Leave blank to keep current password' : 'Minimum 8 characters' }}">
            @error('portal_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>
    </x-admin.form-grid>
</x-admin.form-section>

<x-admin.form-section title="Other">
    <x-admin.form-grid>
        <x-admin.form-field label="Notes" for="notes" class="col-lg-8 col-md-8">
            <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $supplier->notes ?? '') }}</textarea>
            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Status" class="col-lg-4 col-md-4">
            <x-admin.form-switch :checked="$supplier->is_active ?? true" :send-unchecked="true" inline />
        </x-admin.form-field>
    </x-admin.form-grid>
</x-admin.form-section>
