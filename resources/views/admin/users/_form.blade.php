@php $user = $user ?? null; @endphp

<x-admin.form-grid>
    @unless ($user)
        <x-admin.form-field label="Name" for="name" class="col-lg-4 col-md-6" :required="true">
            <input type="text" name="name" id="name" value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

        <x-admin.form-field label="Email" for="email" class="col-lg-4 col-md-6" :required="true">
            <input type="email" name="email" id="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </x-admin.form-field>

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
</x-admin.form-grid>
