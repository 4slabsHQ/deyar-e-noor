@php
    $role = $role ?? null;
    $showSelectAll = $showSelectAll ?? false;
@endphp

<x-admin.form-grid>
    <x-admin.form-field label="Role Name" for="name" class="col-lg-6 col-md-8" :required="true">
        <input type="text" name="name" id="name" value="{{ old('name', $role?->name) }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>
</x-admin.form-grid>

@include('admin.roles._permissions', ['showSelectAll' => $showSelectAll, 'role' => $role])
