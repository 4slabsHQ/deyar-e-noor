@php $tax = $tax ?? null; @endphp

<x-admin.form-grid>
    <x-admin.form-field label="Name" for="name" class="col-lg-4 col-md-6" :required="true">
        <input type="text" name="name" id="name" value="{{ old('name', $tax->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" placeholder="e.g. VAT 5%" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Code" for="code" class="col-lg-2 col-md-3">
        <input type="text" name="code" id="code" value="{{ old('code', $tax->code ?? '') }}"
               class="form-control @error('code') is-invalid @enderror" placeholder="e.g. VAT5">
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Rate" for="rate" class="col-lg-3 col-md-3" :required="true"
                        hint="5 = 5% for percentage, or flat amount for fixed.">
        <input type="number" step="0.0001" min="0" name="rate" id="rate"
               value="{{ old('rate', $tax->rate ?? '') }}"
               class="form-control @error('rate') is-invalid @enderror" required>
        @error('rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Type" for="type" class="col-lg-3 col-md-4" :required="true">
        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
            <option value="percentage" @selected(old('type', $tax->type ?? '') === 'percentage')>Percentage</option>
            <option value="fixed" @selected(old('type', $tax->type ?? '') === 'fixed')>Fixed</option>
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Status" class="col-lg-4 col-md-4">
        <x-admin.form-switch :checked="$tax->is_active ?? true" :send-unchecked="true" inline />
    </x-admin.form-field>
</x-admin.form-grid>
