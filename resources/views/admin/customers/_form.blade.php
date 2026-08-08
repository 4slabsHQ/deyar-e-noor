@php $customer = $customer ?? null; @endphp

<x-admin.form-section title="Basic Information">
    <x-admin.form-grid>
        <x-admin.form-field label="Name" for="name" class="col-lg-4 col-md-6" :required="true">
            <input type="text" name="name" id="name" value="{{ old('name', $customer->name ?? '') }}"
                   class="form-control @error('name') is-invalid @enderror" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Code" for="code" class="col-lg-2 col-md-3">
            <input type="text" name="code" id="code" value="{{ old('code', $customer->code ?? '') }}"
                   class="form-control @error('code') is-invalid @enderror">
            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Customer Type" for="customer_type" class="col-lg-3 col-md-4" :required="true">
            <select name="customer_type" id="customer_type" class="form-control @error('customer_type') is-invalid @enderror" required>
                @foreach (['individual' => 'Individual', 'corporate' => 'Corporate', 'walk_in' => 'Walk-in'] as $value => $label)
                    <option value="{{ $value }}"
                        @selected(old('customer_type', $customer->customer_type ?? 'individual') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('customer_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Company Name" for="company_name" class="col-lg-3 col-md-6" hint="For corporate customers only.">
            <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $customer->company_name ?? '') }}"
                   class="form-control @error('company_name') is-invalid @enderror">
            @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>
    </x-admin.form-grid>
</x-admin.form-section>

<x-admin.form-section title="Contact Details">
    <x-admin.form-grid>
        <x-admin.form-field label="Email" for="email" class="col-lg-4 col-md-6">
            <input type="email" name="email" id="email" value="{{ old('email', $customer->email ?? '') }}"
                   class="form-control @error('email') is-invalid @enderror">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Phone" for="phone" class="col-lg-4 col-md-6">
            <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone ?? '') }}"
                   class="form-control @error('phone') is-invalid @enderror">
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="WhatsApp" for="whatsapp" class="col-lg-4 col-md-6">
            <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $customer->whatsapp ?? '') }}"
                   class="form-control @error('whatsapp') is-invalid @enderror">
            @error('whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <div class="col-12" data-country-city>
            <div class="row g-3">
                <x-admin.form-field label="Country" for="country_id" class="col-lg-6 col-md-6">
                    <select name="country_id" id="country_id" data-country-select
                            class="form-control js-searchable-select @error('country_id') is-invalid @enderror"
                            data-placeholder="Select country">
                        <option value="" disabled @selected(! old('country_id', $customer->country_id ?? ''))>Select</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}"
                                @selected((string) old('country_id', $customer->country_id ?? '') === (string) $country->id)>
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
                        <option value="" disabled @selected(! old('city_id', $customer->city_id ?? ''))>Select</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" data-country-id="{{ $city->country_id }}"
                                @selected((string) old('city_id', $customer->city_id ?? '') === (string) $city->id)>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </x-admin.form-field>
            </div>
        </div>

        <x-admin.form-field label="Address" for="address" class="col-lg-8 col-md-8">
            <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $customer->address ?? '') }}</textarea>
            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Nationality" for="nationality" class="col-lg-4 col-md-4">
            <input type="text" name="nationality" id="nationality" value="{{ old('nationality', $customer->nationality ?? '') }}"
                   class="form-control @error('nationality') is-invalid @enderror">
            @error('nationality') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>
    </x-admin.form-grid>
</x-admin.form-section>

<x-admin.form-section title="Identity Details">
    <x-admin.form-grid>
        <x-admin.form-field label="Passport Number" for="passport_number" class="col-lg-3 col-md-6">
            <input type="text" name="passport_number" id="passport_number" value="{{ old('passport_number', $customer->passport_number ?? '') }}"
                   class="form-control @error('passport_number') is-invalid @enderror">
            @error('passport_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="CNIC" for="cnic" class="col-lg-3 col-md-6">
            <input type="text" name="cnic" id="cnic" value="{{ old('cnic', $customer->cnic ?? '') }}"
                   class="form-control @error('cnic') is-invalid @enderror">
            @error('cnic') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Date of Birth" for="dob" class="col-lg-3 col-md-6">
            <input type="date" name="dob" id="dob" value="{{ old('dob', optional($customer?->dob)->format('Y-m-d')) }}"
                   class="form-control @error('dob') is-invalid @enderror">
            @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Gender" for="gender" class="col-lg-3 col-md-6">
            <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror">
                <option value="" disabled @selected(! old('gender', $customer->gender ?? ''))>Select</option>
                @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                    <option value="{{ $value }}"
                        @selected(old('gender', $customer->gender ?? '') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>
    </x-admin.form-grid>
</x-admin.form-section>

<x-admin.form-section title="Tax & Credit">
    <x-admin.form-grid>
        <x-admin.form-field label="Tax Number" for="tax_number" class="col-lg-4 col-md-6">
            <input type="text" name="tax_number" id="tax_number" value="{{ old('tax_number', $customer->tax_number ?? '') }}"
                   class="form-control @error('tax_number') is-invalid @enderror">
            @error('tax_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Credit Limit" for="credit_limit" class="col-lg-4 col-md-6">
            <input type="number" step="0.01" min="0" name="credit_limit" id="credit_limit"
                   value="{{ old('credit_limit', $customer->credit_limit ?? 0) }}"
                   class="form-control @error('credit_limit') is-invalid @enderror">
            @error('credit_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Credit Days" for="credit_days" class="col-lg-4 col-md-6">
            <input type="number" min="0" name="credit_days" id="credit_days"
                   value="{{ old('credit_days', $customer->credit_days ?? 0) }}"
                   class="form-control @error('credit_days') is-invalid @enderror">
            @error('credit_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>
    </x-admin.form-grid>
</x-admin.form-section>

<x-admin.form-section title="Other">
    <x-admin.form-grid>
        <x-admin.form-field label="Notes" for="notes" class="col-lg-8 col-md-8">
            <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $customer->notes ?? '') }}</textarea>
            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Status" class="col-lg-4 col-md-4">
            <x-admin.form-switch :checked="$customer->is_active ?? true" :send-unchecked="true" inline />
        </x-admin.form-field>
    </x-admin.form-grid>
</x-admin.form-section>
