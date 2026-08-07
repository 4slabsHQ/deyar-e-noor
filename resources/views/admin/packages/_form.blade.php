@php
    use App\Enums\PackageDuration;

    $package = $package ?? null;
@endphp

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Package No <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="number" maxlength="50" value="{{ old('number', $package->number ?? '') }}"
               class="form-control @error('number') is-invalid @enderror" required>
        @error('number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Name <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="text" name="name" value="{{ old('name', $package->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Price <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="number" name="price" step="0.01" min="0" value="{{ old('price', $package->price ?? '') }}"
               class="form-control @error('price') is-invalid @enderror" required>
        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Days <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <input type="number" name="days" min="0" step="1" value="{{ old('days', $package->days ?? '') }}"
               class="form-control @error('days') is-invalid @enderror" required>
        @error('days') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <label class="col-lg-3 col-form-label">Duration <span class="text-danger">*</span></label>
    <div class="col-lg-8">
        <select name="duration" class="form-control @error('duration') is-invalid @enderror" required>
            <option value="">-- Select Duration --</option>
            @foreach (PackageDuration::cases() as $duration)
                <option value="{{ $duration->value }}"
                    {{ old('duration', $package?->duration?->value) === $duration->value ? 'selected' : '' }}>
                    {{ $duration->label() }}
                </option>
            @endforeach
        </select>
        @error('duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3 row">
    <div class="col-lg-3"></div>
    <div class="col-lg-8">
        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="qurbani_included" value="1" id="qurbani_included"
                   {{ old('qurbani_included', $package->qurbani_included ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="qurbani_included">Qurbani Included</label>
        </div>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                   {{ old('is_active', $package->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>
