@props([
    'label',
    'for' => null,
    'required' => false,
    'hint' => null,
])

<div {{ $attributes->merge(['class' => 'admin-form-field']) }}>
    <label
        @if($for) for="{{ $for }}" @endif
        class="admin-form-label form-label"
    >
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    {{ $slot }}
    @if($hint)
        <small class="form-hint">{{ $hint }}</small>
    @endif
</div>
