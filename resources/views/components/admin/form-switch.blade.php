@props([
    'name' => 'is_active',
    'label' => 'Active',
    'id' => null,
    'checked' => true,
    'sendUnchecked' => false,
    'inline' => false,
])

@php
    $fieldId = $id ?? $name;
    $isChecked = (bool) old($name, $checked);
@endphp

@if($inline)
    <div {{ $attributes->merge(['class' => 'admin-form-switch-inline']) }}>
        @if($sendUnchecked)
            <input type="hidden" name="{{ $name }}" value="0">
        @endif
        <div class="form-check form-switch">
            <input
                class="form-check-input"
                type="checkbox"
                name="{{ $name }}"
                id="{{ $fieldId }}"
                value="1"
                @checked($isChecked)
            >
            <label class="form-check-label" for="{{ $fieldId }}">{{ $label }}</label>
        </div>
    </div>
@else
<div {{ $attributes->merge(['class' => 'admin-form-row mb-3 row']) }}>
    <div class="col-lg-3"></div>
    <div class="col-lg-8">
        @if($sendUnchecked)
            <input type="hidden" name="{{ $name }}" value="0">
        @endif
        <div class="form-check form-switch">
            <input
                class="form-check-input"
                type="checkbox"
                name="{{ $name }}"
                id="{{ $fieldId }}"
                value="1"
                @checked($isChecked)
            >
            <label class="form-check-label" for="{{ $fieldId }}">{{ $label }}</label>
        </div>
    </div>
</div>
@endif

