@php $maktabCategory = $maktabCategory ?? null; @endphp

<x-admin.form-grid>
    <x-admin.form-field label="Category" for="name" class="col-lg-6 col-md-6" :required="true">
        <input type="text" name="name" id="name" value="{{ old('name', $maktabCategory->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Zone" for="zone" class="col-lg-4 col-md-4" :required="true">
        <input type="text" name="zone" id="zone" maxlength="50" value="{{ old('zone', $maktabCategory->zone ?? '') }}"
               class="form-control @error('zone') is-invalid @enderror" required>
        @error('zone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Status" class="col-lg-2 col-md-2">
        <x-admin.form-switch :checked="$maktabCategory->is_active ?? true" inline />
    </x-admin.form-field>
</x-admin.form-grid>
