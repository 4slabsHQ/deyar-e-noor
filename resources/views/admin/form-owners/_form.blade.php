@php $record = $formOwner ?? null; @endphp

<x-admin.form-grid>
    <x-admin.form-field label="Name" for="name" class="col-lg-6 col-md-7" :required="true">
        <input type="text" name="name" id="name" value="{{ old('name', $record?->name) }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Limit" for="limit" class="col-lg-2 col-md-3" hint="Max pilgrims per Hajj year. Leave empty for unlimited.">
        <input type="number" name="limit" id="limit" min="1" value="{{ old('limit', $record?->limit) }}"
               class="form-control @error('limit') is-invalid @enderror" placeholder="Unlimited">
        @error('limit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Status" class="col-lg-4 col-md-5">
        <x-admin.form-switch :checked="$record?->is_active ?? true" inline />
    </x-admin.form-field>
</x-admin.form-grid>
