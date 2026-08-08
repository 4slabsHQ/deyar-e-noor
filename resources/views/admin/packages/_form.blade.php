@php
    use App\Enums\PackageDuration;

    $package = $package ?? null;
@endphp

<x-admin.form-grid>
    <x-admin.form-field label="Package No" for="number" class="col-lg-2 col-md-3" :required="true">
        <input type="text" name="number" id="number" maxlength="50" value="{{ old('number', $package->number ?? '') }}"
               class="form-control @error('number') is-invalid @enderror" required>
        @error('number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Name" for="name" class="col-lg-4 col-md-5" :required="true">
        <input type="text" name="name" id="name" value="{{ old('name', $package->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Price" for="price" class="col-lg-2 col-md-2" :required="true">
        <input type="number" name="price" id="price" step="0.01" min="0" value="{{ old('price', $package->price ?? '') }}"
               class="form-control @error('price') is-invalid @enderror" required>
        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Days" for="days" class="col-lg-2 col-md-2" :required="true">
        <input type="number" name="days" id="days" min="0" step="1" value="{{ old('days', $package->days ?? '') }}"
               class="form-control @error('days') is-invalid @enderror" required>
        @error('days') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Duration" for="duration" class="col-lg-2 col-md-4" :required="true">
        <select name="duration" id="duration" class="form-control @error('duration') is-invalid @enderror" required>
            <option value="" disabled @selected(! old('duration', $package?->duration?->value))>Select</option>
            @foreach (PackageDuration::cases() as $duration)
                <option value="{{ $duration->value }}"
                    @selected(old('duration', $package?->duration?->value) === $duration->value)>
                    {{ $duration->label() }}
                </option>
            @endforeach
        </select>
        @error('duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </x-admin.form-field>

    <x-admin.form-field label="Options" class="col-lg-3 col-md-4">
        <div class="form-check form-switch admin-form-switch-inline">
            <input class="form-check-input" type="checkbox" name="qurbani_included" value="1" id="qurbani_included"
                   @checked(old('qurbani_included', $package->qurbani_included ?? false))>
            <label class="form-check-label" for="qurbani_included">Qurbani Included</label>
        </div>
    </x-admin.form-field>

    <x-admin.form-field label="Status" class="col-lg-3 col-md-4">
        <x-admin.form-switch :checked="$package->is_active ?? true" inline />
    </x-admin.form-field>
</x-admin.form-grid>
