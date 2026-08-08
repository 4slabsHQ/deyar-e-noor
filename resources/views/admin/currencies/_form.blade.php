@php $currency = $currency ?? null; @endphp

<x-admin.form-grid>
    <x-admin.form-field label="Name" for="name" class="col-lg-4 col-md-6" :required="true">
        <input type="text" name="name" id="name" value="{{ old('name', $currency->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Code" for="code" class="col-lg-2 col-md-3" :required="true" hint="e.g. USD, PKR">
        <input type="text" name="code" id="code" maxlength="3" value="{{ old('code', $currency->code ?? '') }}"
               class="form-control text-uppercase @error('code') is-invalid @enderror" required>
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Symbol" for="symbol" class="col-lg-2 col-md-3">
        <input type="text" name="symbol" id="symbol" maxlength="10" value="{{ old('symbol', $currency->symbol ?? '') }}"
               class="form-control @error('symbol') is-invalid @enderror" placeholder="$">
        @error('symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Exchange Rate" for="exchange_rate" class="col-lg-4 col-md-6" :required="true">
        <input type="number" step="0.000001" min="0" name="exchange_rate" id="exchange_rate"
               value="{{ old('exchange_rate', $currency->exchange_rate ?? 1) }}"
               class="form-control @error('exchange_rate') is-invalid @enderror" required>
        @error('exchange_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Default" class="col-lg-3 col-md-4">
        <x-admin.form-switch name="is_default" label="Set as Default" :checked="$currency->is_default ?? false" inline />
    </x-admin.form-field>

    <x-admin.form-field label="Status" class="col-lg-3 col-md-4">
        <x-admin.form-switch :checked="$currency->is_active ?? true" inline />
    </x-admin.form-field>
</x-admin.form-grid>
