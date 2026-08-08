@php $permission = $permission ?? null; @endphp

<x-admin.form-grid>
    <x-admin.form-field
        label="Permission Name"
        for="name"
        class="col-lg-6 col-md-8"
        :required="true"
        hint="Use the convention module.action (e.g. bookings.view)."
    >
        <input type="text" name="name" id="name" value="{{ old('name', $permission?->name) }}"
               class="form-control @error('name') is-invalid @enderror"
               placeholder="e.g. invoices.export" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>
</x-admin.form-grid>
