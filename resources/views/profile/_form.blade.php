@php
    $roleName = $user->getRoleNames()->first();
@endphp

<x-admin.form-grid>
    <x-admin.form-field label="Photo" for="photo" class="col-lg-4 col-md-6">
        <x-admin.image-upload
            name="photo"
            remove-name="remove_photo"
            :existing-url="$user->photo_url"
            :existing-filename="$user->photo_path ? basename($user->photo_path) : null"
            accept="image/jpeg,image/jpg,image/png,image/webp"
            hint="JPEG/PNG/WebP, max 5MB"
            upload-label="Upload photo"
            preview-alt="Profile photo"
        />
    </x-admin.form-field>

    <x-admin.form-field label="Name" for="name" class="col-lg-4 col-md-6" :required="true">
        <input
            type="text"
            id="name"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $user->name) }}"
            required
        />
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Email" for="email" class="col-lg-4 col-md-6" :required="true">
        <input
            type="email"
            id="email"
            name="email"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $user->email) }}"
            required
        />
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </x-admin.form-field>

    @if ($roleName)
        <x-admin.form-field label="Role" for="role_display" class="col-lg-4 col-md-6">
            <input
                type="text"
                id="role_display"
                class="form-control"
                value="{{ $roleName }}"
                readonly
            />
        </x-admin.form-field>
    @endif
</x-admin.form-grid>
