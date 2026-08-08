@props([
    'label',
    'for' => null,
    'required' => false,
    'hint' => null,
])

<div {{ $attributes->merge(['class' => 'admin-form-row mb-3 row']) }}>
    <label
        @if($for) for="{{ $for }}" @endif
        class="col-lg-3 col-form-label admin-form-label"
    >
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <div class="col-lg-8">
        {{ $slot }}
        @if($hint)
            <small class="form-hint">{{ $hint }}</small>
        @endif
    </div>
</div>
