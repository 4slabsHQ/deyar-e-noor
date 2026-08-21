@php $user = $user ?? null; @endphp

<x-admin.form-grid>
    <x-admin.form-field label="Photo" for="photo" class="col-lg-4 col-md-6">
        <x-admin.image-upload
            name="photo"
            remove-name="remove_photo"
            :existing-url="$user?->photo_url"
            :existing-filename="$user?->photo_path ? basename($user->photo_path) : null"
            accept="image/jpeg,image/jpg,image/png,image/webp"
            hint="JPEG/PNG/WebP, max 5MB"
            upload-label="Upload photo"
            preview-alt="User photo"
        />
    </x-admin.form-field>

    <x-admin.form-field label="Name" for="name" class="col-lg-4 col-md-6" :required="true">
        <input type="text" name="name" id="name" value="{{ old('name', $user?->name) }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Email" for="email" class="col-lg-4 col-md-6" :required="true">
        <input type="email" name="email" id="email" value="{{ old('email', $user?->email) }}"
               class="form-control @error('email') is-invalid @enderror" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    @unless ($user)
        <x-admin.form-field label="Password" for="password" class="col-lg-4 col-md-6" :required="true">
            <input type="password" name="password" id="password"
                   class="form-control @error('password') is-invalid @enderror" required>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Confirm Password" for="password_confirmation" class="col-lg-4 col-md-6" :required="true">
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
        </x-admin.form-field>
    @endunless

    <x-admin.form-field label="Role" for="role" class="col-lg-4 col-md-6" :required="true">
        <select name="role" id="role"
                class="form-control js-searchable-select @error('role') is-invalid @enderror"
                data-placeholder="Select role" required>
            <option value="" disabled @selected(! old('role', $user?->roles->first()?->name))>Select</option>
            @foreach ($roles as $role)
                <option value="{{ $role->name }}"
                    @selected(old('role', $user?->roles->first()?->name) === $role->name)>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>
        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Status" for="is_active" class="col-lg-4 col-md-6">
        <x-admin.form-switch
            :checked="old('is_active', $user?->is_active ?? true)"
            send-unchecked
            inline
        />
        @error('is_active') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </x-admin.form-field>
</x-admin.form-grid>
